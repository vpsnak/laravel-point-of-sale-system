<template>
  <v-card>
    <v-container>
      <v-row justify="center" align="center">
        <v-col cols="auto">
          <v-icon v-if="data_table.icon" class="mr-2">
            {{ data_table.icon }}
          </v-icon>
          {{ data_table.title }}
        </v-col>

        <v-col cols="auto">
          <v-tooltip bottom v-if="data_table.refreshBtn">
            <template v-slot:activator="{ on }">
              <v-btn
                :loading="data_table.loading"
                :disabled="data_table.loading"
                @click.stop="getItems(true)"
                icon
                v-on="on"
                color="green"
              >
                <v-icon>
                  mdi-refresh
                </v-icon>
              </v-btn>
            </template>
            <span>Refresh</span>
          </v-tooltip>
        </v-col>

        <v-col cols="auto" v-if="data_table.filters">
          <component
            :is="data_table.filters"
            @applyFilters="(applied_filters = $event), getItems(true)"
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
                getItems(search)
            "
            @click:prepend-inner="getItems(search)"
            @keyup.enter="
              (keyword = searchValue), (search = true), getItems(search)
            "
          ></v-text-field>
        </v-col>
        <v-spacer />

        <v-divider
          class="mx-4"
          v-if="data_table.newForm && data_table.btnTxt"
          inset
          vertical
        />
        <v-col cols="auto">
          <v-btn
            v-if="data_table.newForm && data_table.btnTxt"
            :disabled="data_table.disableNewBtn || data_table.loading"
            color="primary"
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
            loading-text=" "
            :headers="getHeaders"
            :items="data_table.items"
            :loading="data_table.loading"
          >
            <template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
              <slot :name="slot" v-bind="scope" />
            </template>

            <!-- <template v-slot:item.created_at="{ item }">
          <timestampChip :timestamp="item.created_at" />
        </template>

        <template v-slot:item.created_at="{ item }">
          <timestampChip :timestamp="item.updated_at" />
        </template> -->

            <v-alert
              :value="true"
              color="error"
              icon="warning"
              slot="no-results"
            >
              Your search for "{{ keyword }}" found no results.
            </v-alert>
          </v-data-table>
          <v-card-actions>
            <v-pagination
              v-model="page"
              :length="pageCount"
              @input="getItems(search)"
            ></v-pagination>
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
      overlay: false,
      page: 1,
      pageCount: null,
      action: "",
      searchValue: "",
      search: false,
      keyword: null,
      applied_filters: null
    };
  },

  mounted() {
    this.initEvents();
    this.getItems();
  },

  beforeDestroy() {
    EventBus.$off("data-table");
    if (this.data_table.newForm === "productForm") {
      this.$root.$off("barcodeScan");
    }
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
              this.getItems(true);
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
    getItems(search = false) {
      this.setLoading(true);
      this.setItems([]);
      this.pageCount = null;
      this.searchValue = this.keyword;

      let payload = {};

      if (search && (this.keyword || this.applied_filters)) {
        payload.method = "post";
        payload.url = `${this.data_table.model}/search?page=${this.page}`;
        payload.data = {
          keyword: this.keyword ? this.keyword : ""
        };
        payload.data.filters = this.applied_filters;
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
          this.setLoading(false);
        });
    },
    createItemDialog() {
      this.setDialog({
        show: true,
        width: 600,
        title: this.data_table.btnTxt,
        titleCloseBtn: true,
        component: this.data_table.newForm,
        persistent: true,
        eventChannel: "data-table"
      });
    }
  }
};
</script>
