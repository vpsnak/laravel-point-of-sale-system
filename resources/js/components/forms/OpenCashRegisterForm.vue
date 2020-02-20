<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
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
        <ValidationProvider
          v-if="!cashRegisterIsopen"
          rules="required|between:1,9999"
          v-slot="{ errors, valid }"
          name="Name"
        >
          <v-text-field
            v-if="!cashRegisterIsopen"
            :disabled="openingAmountDisabled"
            v-model="opening_amount"
            type="number"
            label="Opening amount"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
      </v-container>
      <v-container>
        <v-row justify="center" align="center">
          <v-col
            :cols="6"
            v-if="
              remainingAmount && selected_cash_register_id && cashRegisterIsopen
            "
          >
            <span class="title">
              Amount:
              <span
                class="amber--text"
                v-text="'$ ' + remainingAmount.toFixed(2)"
              />
            </span>
          </v-col>
          <v-col :cols="6">
            <v-btn
              color="secondary"
              type="submit"
              :loading="loading"
              :disabled="disableOpenCashRegister || invalid"
              >Open Cash Register
            </v-btn>
          </v-col>
        </v-row>
        <v-alert v-if="cashRegisterIsopen" dense outlined type="warning">
          Warning: The selected cash register is already
          <strong>open</strong>
        </v-alert>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>
<script>
import { mapActions, mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      loading: true,
      stores: [],
      cash_registers: [],
      storeDisabled: true,
      cashRegisterDisabled: true,
      openingAmountDisabled: true,
      selected_store_id: null,
      selected_cash_register_id: null,
      opening_amount: null,
      status: true,
      remainingAmount: null
    };
  },
  mounted() {
    this.getStores();
  },
  computed: {
    cashRegisterIsopen() {
      const open_cash_register = _.find(this.cash_registers, {
        id: this.selected_cash_register_id,
        is_open: true
      });
      if (open_cash_register) {
        this.remainingAmount = open_cash_register.earnings.cash_total;
        return true;
      } else {
        this.remainingAmount = 0;
        return false;
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
    },
    role() {
      return this.$store.getters.role;
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
        endpoint: "stores"
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
          opening_amount: this.opening_amount,
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
    getAllStores() {
      this.getAll({
        model: "stores"
      }).then(stores => {
        this.stores = stores;
      });
    },
    getAllCashRegisters() {
      this.loading = true;
      this.getAll({
        model: "cash-registers"
      })
        .then(cash_registers => {
          this.cash_registers = cash_registers;
        })
        .finally(() => {
          this.loading = false;
        });
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
  },

  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
