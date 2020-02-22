<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <v-row align="center" justify="center" no-gutters>
          <v-col :cols="12">
            <ValidationProvider
              rules="required"
              v-slot="{ errors, valid }"
              name="Store"
            >
              <v-select
                :loading="loading"
                v-model="selected_store_id"
                :disabled="storeDisabled"
                :items="stores"
                label="Store"
                item-text="name"
                item-value="id"
                @change="changeCashRegisters"
                @input="enableCashRegisters"
                :error-messages="errors"
                :success="valid"
              ></v-select>
            </ValidationProvider>
          </v-col>
          <v-col :cols="12">
            <ValidationProvider
              rules="required"
              v-slot="{ errors, valid }"
              name="Cash Register"
            >
              <v-select
                :loading="loading"
                :disabled="cashRegisterDisabled"
                v-model="selected_cash_register_id"
                :items="cash_registers"
                label="Cash Register"
                item-text="name"
                item-value="id"
                @input="enableOpeningAmount"
                :error-messages="errors"
                :success="valid"
              ></v-select>
            </ValidationProvider>
          </v-col>
        </v-row>
        <v-row align="center" justify="space-between" no-gutters>
          <v-col :cols="4" :lg="2" :md="3">
            <ValidationProvider
              v-if="fill_amount || !cashRegisterIsopen"
              rules="required|between:1,9999"
              v-slot="{ errors, valid }"
              name="Opening amount"
            >
              <v-text-field
                :disabled="openingAmountDisabled"
                v-model="opening_amount"
                prefix="$"
                type="number"
                label="Amount"
                hint="Opening amount"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-btn
            color="secondary"
            type="submit"
            :loading="loading"
            :disabled="disableOpenCashRegister || invalid"
            >Start session
          </v-btn>
        </v-row>
        <v-row align="center" justify="center">
          <v-col cols="auto">
            <v-alert
              text
              prominent
              :type="open_session_user ? 'warning' : 'info'"
              v-if="cashRegisterIsopen"
            >
              <span v-if="open_session_user">
                {{ open_session_user.name }} has an active session with the
                selected cash register
              </span>
              <span v-else>
                The selected cash register is already open
                <br />No user has an active session
              </span>
            </v-alert>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>
<script>
import { mapActions, mapState, mapMutations, mapGetters } from "vuex";

export default {
  data() {
    return {
      fill_amount: false,
      open_session_user: null,
      loading: true,
      stores: [],
      cash_registers: [],
      storeDisabled: true,
      cashRegisterDisabled: true,
      openingAmountDisabled: true,
      selected_store_id: null,
      selected_cash_register_id: null,
      opening_amount: null,
      status: true
    };
  },

  mounted() {
    this.getStores();
  },

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    ...mapGetters(["role"]),

    cashRegisterIsopen() {
      if (this.selected_store_id && this.selected_cash_register_id) {
        const open_cash_register = _.find(this.cash_registers, {
          id: this.selected_cash_register_id,
          is_open: true
        });
        if (open_cash_register) {
          if (_.has(open_cash_register, "open_session_user")) {
            this.open_session_user = open_cash_register.open_session_user;
          } else {
            this.open_session_user = null;
          }

          return true;
        } else {
          this.fill_amount = true;
          return false;
        }
      }
    },
    disableOpenCashRegister() {
      if (
        (this.selected_store_id && this.opening_amount) ||
        this.cashRegisterIsopen
      ) {
        return false;
      } else {
        return true;
      }
    }
  },

  methods: {
    ...mapMutations(["setCashRegister"]),
    ...mapMutations("menu", ["setStoreName"]),
    ...mapMutations("cart", ["setTaxPercentage"]),

    ...mapActions("requests", ["request"]),
    ...mapActions(["openCashRegister"]),

    getStores() {
      this.request({
        method: "get",
        url: "stores"
      })
        .then(response => {
          this.stores = response;

          if (this.role == "admin") {
            this.storeDisabled = false;
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    submit() {
      this.loading = true;

      let payload = {
        data: {
          store_id: this.selected_store_id,
          cash_register_id: this.selected_cash_register_id,
          opening_amount: this.opening_amount || null,
          status: this.status
        }
      };
      this.openCashRegister(payload)
        .then(response => {
          this.$emit("submit", true);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    changeCashRegisters() {
      this.fill_amount = false;
      this.opening_amount = null;
      this.cash_registers = [];
      this.selected_cash_register_id = null;
      this.stores.forEach(store => {
        if (store.id === this.selected_store_id) {
          this.cash_registers = store.cash_registers;
          return;
        }
      });
    },
    cashierDisabled() {
      if (this.role == "admin" || this.role == "store_manager") {
        return false;
      } else {
        return true;
      }
    },
    enableCashRegisters() {
      this.cashRegisterDisabled = false;
    },

    enableOpeningAmount() {
      this.openingAmountDisabled = false;
    },
    barcodeHandling(barcode) {
      this.cash_registers.forEach(cash_register => {
        if (cash_register.barcode === barcode) {
          this.selected_cash_register_id = cash_register.id;
          this.selected_store_id = cash_register.store.id;
          this.storeDisabled = true;
          if (cash_register.is_open === false) {
            this.enableOpeningAmount();
          }
        }
      });
    }
  }
};
</script>
