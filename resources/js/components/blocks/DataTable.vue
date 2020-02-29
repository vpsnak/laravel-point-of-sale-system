<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        <v-row justify="center" align="center">
          <v-icon v-if="data_table.icon" class="mr-2">
            {{ data_table.icon }}
          </v-icon>
          {{ data_table.title }}

          <v-spacer />

          <dataTableFilters v-if="data_table.filters" />

          <v-text-field
            ref="searchInput"
            :disabled="data_table.loading"
            prepend-icon="search"
            hide-details
            label="Search"
            single-line
            v-model="searchValue"
            clearable
            @click:clear="
              (page = 1),
                (keyword = searchValue = null),
                (search = false),
                getItems(search)
            "
            @click:prepend="getItems(search)"
            @keyup.enter="
              (keyword = searchValue), (search = true), getItems(search)
            "
          ></v-text-field>
          <v-divider
            class="mx-4"
            v-if="data_table.newForm && data_table.btnTxt"
            inset
            vertical
          ></v-divider>
          <v-btn
            v-if="data_table.newForm && data_table.btnTxt"
            :disabled="data_table.disableNewBtn || data_table.loading"
            color="primary"
            @click="createItemDialog()"
          >
            {{ data_table.btnTxt }}
          </v-btn>
        </v-row>
      </v-card-title>

      <v-data-table
        fixed-header
        disable-sort
        dense
        disable-filtering
        :headers="getHeaders"
        :items="data_table.items"
        :loading="data_table.loading"
        disable-pagination
        hide-default-footer
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

        <v-alert :value="true" color="error" icon="warning" slot="no-results">
          Your search for "{{ keyword }}" found no results.
        </v-alert>
      </v-data-table>
      <v-card-actions>
        <v-pagination
          v-model="page"
          :length="pageCount"
          @input="getItems(search)"
          @next="page++"
          @previous="page--"
        ></v-pagination>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
  data() {
    return {
      page: 1,
      pageCount: null,
      action: "",
      searchValue: "",
      search: false,
      keyword: null
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
      EventBus.$on("data-table", event => {
        if (_.has(event, "payload.action")) {
          switch (event.payload.action) {
            case "paginate":
              this.getItems();
              break;
            case "search":
              this.keyword = event.keyword || null;
              this.search();
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

      if (search && this.keyword) {
        payload.method = "post";
        payload.url = `${this.data_table.model}/search?page=${this.page}`;
        payload.data = { keyword: this.keyword };
      } else {
        payload.method = "get";
        payload.url = `${this.data_table.model}?page=${this.page}`;
      }

      this.request(payload)
        .then(response => {
          this.setItems(response.data);

          this.pageCount = response.last_page;
        })
        .catch()
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
