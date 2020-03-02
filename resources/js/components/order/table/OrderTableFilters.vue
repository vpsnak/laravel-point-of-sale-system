<template>
  <div class="text-center">
    <v-menu
      v-model="filters_menu"
      offset-y
      :close-on-content-click="false"
      :close-on-click="interactive_dialog.show ? false : true"
    >
      <template v-slot:activator="{ on }">
        <v-btn icon v-on="on" :disabled="data_table.loading">
          <v-icon>
            mdi-filter-outline
          </v-icon>
        </v-btn>
      </template>
      <v-card
        width="450"
        class="pa-5"
        outlined
        :ripple="false"
        :disabled="data_table.loading"
      >
        <v-form @submit.prevent="submit">
          <v-container fluid>
            <v-row justify="space-between" align="center" dense>
              <v-col :cols="12">
                <v-icon left>mdi-calendar-plus</v-icon>
                Submitted
              </v-col>
              <v-col :cols="2">
                <v-checkbox v-model="filters.cb_timestamps"> </v-checkbox>
              </v-col>
              <v-col :cols="5">
                <v-menu
                  v-model="datePickerFrom"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      readonly
                      clearable
                      :value="fromTimestampFormatted"
                      label="From"
                      prepend-inner-icon="mdi-calendar-import"
                      v-on="on"
                      @blur="parseDate"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="filters.timestamp_from"
                    @change="filters.timestamp_to = null"
                    :max="new Date().toJSON()"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col :cols="5">
                <v-menu
                  v-model="datePickerTo"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      readonly
                      clearable
                      :value="toTimestampFormatted"
                      label="To"
                      prepend-inner-icon="mdi-calendar-export"
                      v-on="on"
                      @blur="parseDate"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="filters.timestamp_to"
                    :min="
                      filters.timestamp_from ? filters.timestamp_from : null
                    "
                    :max="new Date().toJSON()"
                  ></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
            <v-row justify="center" align="center" dense>
              <v-col :cols="12">
                <v-icon left>mdi-account-outline</v-icon>
                Customer
              </v-col>
              <v-col :cols="2">
                <v-checkbox v-model="filters.cb_customer"> </v-checkbox>
              </v-col>
              <v-col :cols="10">
                <v-combobox
                  ref="searchfield"
                  :no-filter="true"
                  v-model="selectedCustomer"
                  clearable
                  :items="customer_results"
                  :loading="customer_search_loading"
                  :search-input.sync="search"
                  hide-no-data
                  hide-selected
                  :item-text="getCustomerFullname"
                  placeholder="Start typing to search"
                  return-object
                  @blur="checkIfObjectEvent"
                >
                  <template slot="append-outer">
                    <v-btn
                      @click.stop="viewCustomer()"
                      :disabled="!selectedCustomer"
                      icon
                    >
                      <v-icon>mdi-eye</v-icon>
                    </v-btn>
                  </template>
                </v-combobox>
              </v-col>
            </v-row>
            <v-row dense justify="center" align="center">
              <v-col :cols="12">
                <v-icon left>mdi-file-tree</v-icon>
                Statuses
              </v-col>
              <v-col :cols="2">
                <v-checkbox v-model="filters.cb_statuses" />
              </v-col>
              <v-col :cols="10" align-self="center">
                <v-select
                  multiple
                  dense
                  single-line
                  :items="order_statuses"
                  v-model="selectedStatuses"
                  :loading="order_statuses_loading"
                  chips
                  clearable
                  deletable-chips
                  full-width
                  small-chips
                  item-value="id"
                >
                </v-select>
              </v-col>
            </v-row>
            <v-row justify="space-around" align="center">
              <v-btn type="submit" text color="green">Apply</v-btn>
              <v-btn @click.stop="clear()" text color="red">Clear</v-btn>
            </v-row>
          </v-container>
        </v-form>
      </v-card>
    </v-menu>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
export default {
  beforeDestroy() {
    this.$off("applyFilters");
  },

  mounted() {
    this.getStatuses();
  },

  data() {
    return {
      filters_menu: false,

      order_statuses_loading: false,
      customer_search_loading: false,

      datePickerFrom: false,
      datePickerTo: false,

      order_statuses: null,

      filters: {
        cb_timestamps: false,
        cb_statuses: false,
        cb_customer: false,

        timestamp_from: null,
        timestamp_to: null,
        statuses: null,
        customer_id: null
      },

      customer_results: [],
      selected_customer: null,
      search: null
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"]),
    ...mapState("dialog", ["interactive_dialog"]),

    selectedCustomer: {
      get() {
        return this.selected_customer;
      },
      set(value) {
        this.selected_customer = value;
        if (_.has(value, "id")) {
          this.filters.customer_id = value.id;
          this.filters.cb_customer = true;
        } else {
          this.filters.cb_customer = false;
        }
      }
    },
    selectedStatuses: {
      get() {
        return this.filters.statuses;
      },
      set(value) {
        this.filters.statuses = value;

        if (!value || !value.length) {
          this.filters.cb_statuses = false;
        } else {
          this.filters.cb_statuses = true;
        }
      }
    },
    fromTimestampFormatted() {
      if (this.filters.timestamp_from) {
        return this.parseDate(this.filters.timestamp_from);
      } else {
        return null;
      }
    },
    toTimestampFormatted() {
      if (this.filters.timestamp_to) {
        return this.parseDate(this.filters.timestamp_to);
      } else {
        return null;
      }
    }
  },

  watch: {
    toTimestampFormatted(value) {
      if (value) {
        this.filters.cb_timestamps = true;
      } else {
        this.filters.timestamp_to = null;
        if (!this.fromTimestampFormatted) {
          this.filters.cb_timestamps = false;
        }
      }
    },
    fromTimestampFormatted(value) {
      if (value) {
        this.filters.cb_timestamps = true;
      } else {
        this.filters.timestamp_from = null;
        if (!this.toTimestampFormatted) {
          this.filters.cb_timestamps = false;
        }
      }
    },
    search(keyword) {
      if (keyword && keyword.length >= 3) {
        if (this.customer_search_loading) {
          return;
        } else {
          this.searchCustomer(keyword);
          return;
        }
      }
    },
    customer(value) {
      if (!value) {
        this.$refs.searchfield.lazySearch = this.$refs.searchfield.lazyValue = null;
      }
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("requests", ["request"]),

    submit() {
      this.$emit("applyFilters", this.filters);
      this.filters_menu = false;
    },
    viewCustomer() {
      const dialog = {
        show: true,
        width: 600,
        fullscreen: false,
        icon: "mdi-account-outline",
        title: `View customer #${this.selectedCustomer.full_name}`,
        titleCloseBtn: true,
        component: "customerForm",
        model: this.selectedCustomer,
        persistent: false
      };
      this.setDialog(dialog);
    },
    getStatuses() {
      this.order_statuses_loading = true;
      const payload = {
        method: "get",
        url: "statuses"
      };

      this.request(payload)
        .then(response => {
          this.order_statuses = response;
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          this.order_statuses_loading = false;
        });
    },
    parseDate(d) {
      if (d.length) {
        const [year, month, day] = d.split("-");
        return `${month}/${day}/${year}`;
      } else {
        return null;
      }
    },
    clear() {
      this.search = null;
      this.customer_results = [];

      this.filters.timestamp_from = null;
      this.filters.timestamp_to = null;
      this.selectedCustomer = null;
      this.selectedStatuses = null;
    },
    checkIfObjectEvent() {
      if (!this.selectedCustomer) {
        this.search = null;
      }
    },
    getCustomerFullname(item) {
      if (_.has(item, "full_name")) {
        return `${item.full_name}`;
      } else {
        return "Guest";
      }
    },
    searchCustomer(keyword) {
      this.customer_search_loading = true;

      const payload = {
        method: "post",
        url: "customers/search",
        data: { keyword: keyword }
      };
      this.request(payload)
        .then(result => {
          this.customer_results = result;
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.customer_search_loading = false;
        });
    }
  }
};
</script>
