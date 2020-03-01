<template>
  <div class="text-center">
    <v-menu offset-y :close-on-content-click="false">
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
            <v-row justify="space-between" align="center" no-gutters>
              <v-col :cols="12">
                <v-icon left>mdi-calendar-plus</v-icon>
                Submitted
              </v-col>
              <v-col :cols="2">
                <v-checkbox v-model="timestamps"> </v-checkbox>
              </v-col>
              <v-col :cols="5" class="pr-2">
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
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col :cols="5" class="pl-2">
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
            <v-row no-gutters justify="center" align="center">
              <v-col :cols="12">
                <v-icon left>mdi-account-outline</v-icon>
                Customer
              </v-col>
              <v-col :cols="2">
                <v-checkbox v-model="customer"> </v-checkbox>
              </v-col>
              <v-col :cols="10">
                <v-combobox
                  ref="searchfield"
                  :no-filter="true"
                  v-model="filters.customer_id"
                  clearable
                  :items="customer_results"
                  :loading="customer_search_loading"
                  :search-input.sync="search"
                  hide-no-data
                  hide-selected
                  :item-text="getCustomerFullname"
                  placeholder="Start typing to search"
                  item-value="id"
                  @blur="checkIfObjectEvent"
                ></v-combobox>
              </v-col>
            </v-row>
            <v-row no-gutters justify="center" align="center">
              <v-col :cols="12">
                <v-icon left>mdi-file-tree</v-icon>
                Statuses
              </v-col>
              <v-col :cols="2">
                <v-simple-checkbox v-model="statuses" dense>
                </v-simple-checkbox>
              </v-col>
              <v-col :cols="10" align-self="center">
                <v-select
                  multiple
                  dense
                  single-line
                  :items="order_statuses"
                  v-model="filters.statuses"
                  :loading="order_statuses_loading"
                  chips
                  clearable
                  deletable-chips
                  full-width
                  small-chips
                >
                </v-select>
              </v-col>
            </v-row>
            <v-row justify="space-around" align="center">
              <v-btn @click.stop="reset" text color="red">Clear </v-btn>
              <v-btn type="submit" text color="green">Apply </v-btn>
            </v-row>
          </v-container>
        </v-form>
      </v-card>
    </v-menu>
  </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
  mounted() {
    this.getStatuses();
  },

  data() {
    return {
      order_statuses_loading: false,
      customer_search_loading: false,

      timestamps: false,
      statuses: false,
      customer: false,

      datePickerFrom: false,
      datePickerTo: false,

      order_statuses: null,

      filters: {
        timestamp_from: null,
        timestamp_to: null,
        statuses: null,
        customer_id: null
      },

      customer_results: [],
      search: null
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"]),

    selectedCustomer() {
      return this.filters.customer_id;
    },
    selectedStatuses() {
      return this.filters.statuses;
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
    selectedCustomer(value) {
      if (!value) {
        this.customer = false;
      } else {
        this.customer = true;
      }
    },
    selectedStatuses(value) {
      if (!value || !value.length) {
        this.statuses = false;
      } else {
        this.statuses = true;
      }
    },
    toTimestampFormatted(value) {
      if (value) {
        this.timestamps = true;
      } else {
        if (!this.fromTimestampFormatted) {
          this.timestamps = false;
        }
      }
    },
    fromTimestampFormatted(value) {
      if (value) {
        this.timestamps = true;
      } else {
        if (!this.toTimestampFormatted) {
          this.timestamps = false;
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
    ...mapActions("requests", ["request"]),

    submit() {},
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
    reset() {
      this.timestamps = false;
      this.statuses = false;

      this.filters.timestamp_from = null;
      this.filters.timestamp_to = null;
      this.filters.statuses = null;
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
