<template>
  <v-card class="fill-height">
    <v-container fluid>
      <v-row justify="center" align="center">
        <v-col cols="auto">
          <v-icon v-if="data_table.icon" class="mx-2">
            {{ data_table.icon }}
          </v-icon>
          {{ data_table.title }}
        </v-col>

        <v-col cols="auto">
          <v-tooltip bottom v-if="data_table.refreshBtn">
            <template v-slot:activator="{ on }">
              <v-btn
                :loading="refreshLoading"
                :disabled="data_table.loading"
                @click.stop="(refreshLoading = true), getItems()"
                icon
                v-on="on"
                color="primary"
              >
                <v-icon v-text="'mdi-refresh'" />
              </v-btn>
            </template>
            <span v-text="'Refresh'" />
          </v-tooltip>
        </v-col>

        <v-col cols="auto" v-if="data_table.filters">
          <component
            :is="data_table.filters"
            @applyFilters="(appliedFilters = $event), getItems()"
          />
        </v-col>

        <v-spacer />

        <v-col cols="auto">
          <v-text-field
            ref="searchInput"
            :disabled="data_table.loading"
            prepend-inner-icon="search"
            hide-details
            label="Search"
            dense
            outlined
            solo
            v-model="searchValue"
            clearable
            @click:clear="
              (page = 1),
                (keyword = searchValue = null),
                (search = false),
                getItems()
            "
            @click:prepend-inner="getItems()"
            @keyup.enter="(keyword = searchValue), (search = true), getItems()"
          ></v-text-field>
        </v-col>

        <v-spacer />

        <v-col cols="auto">
          <v-btn
            v-if="data_table.newForm && data_table.btnTxt"
            :disabled="data_table.disableNewBtn || data_table.loading"
            color="primary"
            outlined
            @click="createItemDialog()"
          >
            {{ data_table.btnTxt }}
          </v-btn>
        </v-col>
      </v-row>
      <v-row no-gutters>
        <v-col :cols="12">
          <v-data-table
            fixed-header
            disable-sort
            dense
            disable-filtering
            disable-pagination
            hide-default-footer
            :headers="getHeaders"
            :items="data_table.items"
            :loading="data_table.loading"
          >
            <template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
              <slot :name="slot" v-bind="scope" />
            </template>

            <!-- generic fields -->
            <template v-slot:item.created_at="{ item }">
              <timestampChip :timestamp="item.created_at" small />
            </template>

            <template v-slot:item.updated_at="{ item }">
              <timestampChip :timestamp="item.updated_at" small />
            </template>

            <template v-slot:item.email="{ item }">
              <a :href="'mailto:' + item.email">{{ item.email }}</a>
            </template>
            <template v-slot:item.phone="{ item }">
              <a :href="'tel:' + item.phone">{{ item.phone }}</a>
            </template>
            <!-- generic fields end -->

            <template v-slot:no-data v-if="!data_table.loading">
              <v-row justify="center" align="center" style="height:200px">
                <v-alert
                  v-if="search && keyword"
                  type="warning"
                  border="left"
                  colored-border
                  elevation="3"
                  dense
                  max-width="300px"
                >
                  Your search for "{{ keyword }}" found no results
                </v-alert>
                <v-alert
                  v-else
                  type="info"
                  border="left"
                  colored-border
                  elevation="3"
                  dense
                  max-width="300px"
                >
                  No data
                </v-alert>
              </v-row>
            </template>
          </v-data-table>
          <v-card-actions v-show="pageCount > 1">
            <v-pagination
              v-model="page"
              :length="pageCount"
              @input="getItems()"
            />
          </v-card-actions>
        </v-col>
      </v-row>
      <v-overlay :value="overlay" />
    </v-container>
  </v-card>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import { EventBus } from "../../plugins/eventBus";

export default {
  data() {
    return {
      refreshLoading: false,
      overlay: false,
      page: 1,
      pageCount: null,
      action: "",
      searchValue: "",
      search: false,
      keyword: null,
      appliedFilters: null
    };
  },

  mounted() {
    this.initEvents();
    this.getItems();
  },

  beforeDestroy() {
    EventBus.$off("data-table");
    this.resetDataTable();
  },

  computed: {
    ...mapGetters("datatable", ["getHeaders"]),
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("datatable", ["setItems", "setLoading", "resetDataTable"]),
    ...mapActions("requests", ["request"]),

    initEvents() {
      EventBus.$on("overlay", event => {
        this.overlay = event;
      });
      EventBus.$on("data-table", event => {
        if (_.has(event, "payload.action")) {
          switch (event.payload.action) {
            case "paginate":
              this.getItems();
              break;
            case "search":
              this.keyword = event.keyword || null;
              this.getItems();
              break;
            default:
              if (_.isBoolean(event.payload) && event.payload) {
                this.getItems();
              }
              break;
          }
        } else if (event.payload) {
          console.warn(["Unknown event", { component: "data-table", event }]);
        }
      });

      if (this.data_table.newForm === "productForm") {
        this.$root.$on("barcodeScan", sku => {
          this.keyword = sku;
          this.getItems();
        });
      }
    },
    getItems() {
      this.setLoading(true);
      this.setItems([]);
      this.pageCount = null;
      this.searchValue = this.keyword;

      let payload = {};

      if ((this.search && this.keyword) || this.appliedFilters) {
        payload.method = "post";
        payload.url = `${this.data_table.model}/search?page=${this.page}`;
        payload.data = {
          keyword: this.keyword ? this.keyword : ""
        };
        payload.data.filters = this.appliedFilters;
      } else {
        payload.method = "get";
        payload.url = `${this.data_table.model}?page=${this.page}`;
      }

      this.request(payload)
        .then(response => {
          this.setItems(response.data);

          this.pageCount = response.last_page;
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.refreshLoading = false;
          this.setLoading(false);
        });
    },
    createItemDialog() {
      const payload = {
        show: true,
        width: 600,
        title: this.data_table.btnTxt,
        titleCloseBtn: true,
        component: this.data_table.newForm,
        persistent: true,
        eventChannel: "data-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
