<template>
  <div>
    <v-container fluid>
      <v-row dense justify="center" align="center">
        <v-col :cols="12" justify="center" align="center">
          <h3 class="py-2">Methods</h3>
          ​<v-progress-circular
            v-if="payment_types_loading"
            indeterminate
            color="secondary"
          ></v-progress-circular>
          <v-btn-toggle v-model="paymentType" mandatory @change="clearState">
            <v-btn
              v-for="paymentType in paymentTypes"
              :key="paymentType.id"
              :value="paymentType.type"
              :disabled="loading || orderLoading"
            >
              <v-icon class="pr-2">{{ paymentType.icon }}</v-icon>
              {{ paymentType.name }}
            </v-btn>
          </v-btn-toggle>
        </v-col>
      </v-row>
    </v-container>
    <v-container fluid class="overflow-y-auto" style="max-height: 20vh">
      <v-row
        justify="center"
        align="center"
        v-if="paymentType === 'card'"
        dense
      >
        <v-col :lg="3" :md="6">
          <v-text-field
            dense
            autocomplete="off"
            label="Card number"
            type="number"
            prepend-inner-icon="mdi-credit-card"
            v-model="card.number"
            :disabled="loading || orderLoading"
          ></v-text-field>
        </v-col>
        <v-col :lg="3" :md="6">
          <v-text-field
            dense
            autocomplete="off"
            label="Card holder's name"
            prepend-inner-icon="mdi-account-box"
            v-model="card.card_holder"
            :disabled="loading || orderLoading"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row
        justify="center"
        align="center"
        v-if="paymentType === 'card'"
        dense
      >
        <v-col :lg="3" :md="6">
          <v-text-field
            dense
            autocomplete="off"
            :disabled="loading || orderLoading"
            label="Exp date"
            v-model="card.exp_date"
            prepend-inner-icon="mdi-calendar"
          ></v-text-field>
        </v-col>
        ​
        <v-col :lg="3" :md="6">
          <v-text-field
            dense
            autocomplete="off"
            label="CVC/CVV"
            type="number"
            prepend-inner-icon="mdi-lock"
            v-model="card.cvc"
            :disabled="loading || orderLoading"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row
        justify="center"
        align="center"
        v-else-if="paymentType === 'coupon' || paymentType === 'giftcard'"
      >
        <v-col :lg="3" :md="6">
          <v-text-field
            dense
            label="Code"
            :prepend-inner-icon="getIcon"
            :disabled="loading || orderLoading"
            v-model="code"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" v-if="paymentType !== 'coupon'">
        <v-col :lg="2" :md="3">
          <v-text-field
            prepend-inner-icon="mdi-currency-usd"
            :value="order_remaining_price.toFormat('0.00')"
            disabled
            label="Remaining Amount"
          ></v-text-field>
        </v-col>
        <v-col :lg="2" :md="3">
          <v-text-field
            :disabled="loading || orderLoading"
            :min="0.01"
            :max="9999"
            label="Payment"
            type="number"
            prepend-inner-icon="mdi-currency-usd"
            v-model="amount"
            @keyup="max"
            @keyup.enter="sendPayment"
            @blur="max"
          ></v-text-field>
        </v-col>
      </v-row>
    </v-container>
    ​
    <v-container fluid>
      <v-row justify="center" align="center" class="my-3" dense>
        <v-col :lg="4" :md="6">
          <v-btn
            dark
            block
            color="green darken-3"
            @click="sendPayment"
            :loading="loading || orderLoading"
            :disabled="
              loading || orderLoading || !$store.state.cart.isValidCheckout
            "
          >
            Make a payment
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
​
<script>
import { mapActions, mapState, mapMutations } from "vuex";

export default {
  props: {
    loading: Boolean
  },

  created() {
    this.paymentAmount = this.order_remaining_price;
  },

  mounted() {
    this.getPaymentTypes();
  },

  watch: {
    order_remaining_price(value) {
      if (this.order_status === "pending_payment" || !this.order_status) {
        this.paymentAmount = value;
      }
    }
  },

  beforeDestroy() {
    this.$off("sendPayment");
  },

  data() {
    return {
      payment_types_loading: false,
      payment_types: [],
      orderLoading: false,
      paymentAmount: null,
      paymentType: null,
      code: null,

      card: {
        card_holder:
          process.env.NODE_ENV === "development" ? "John Longjohn" : null,
        number:
          process.env.NODE_ENV === "development" ? "4000000000000002" : null,
        cvc: process.env.NODE_ENV === "development" ? "123" : null,
        exp_date: process.env.NODE_ENV === "development" ? "1224" : null
      }
    };
  },

  computed: {
    ...mapState("cart", [
      "order_remaining_price",
      "order_id",
      "order_total_price",
      "order_status",
      "customer"
    ]),

    getIcon() {
      return _.find(this.paymentTypes, ["type", this.paymentType]).icon;
    },
    paymentTypes: {
      get() {
        if (this.houseAccount) {
          return this.payment_types;
        } else {
          return _.filter(this.payment_types, o => {
            return o.type !== "house-account";
          });
        }
      },
      set(value) {
        this.payment_types = value;
      }
    },
    houseAccountNumber() {
      if (this.houseAccount) {
        return this.customer.house_account_number;
      } else {
        return false;
      }
    },
    houseAccount() {
      if (
        this.customer &&
        this.customer.house_account_status &&
        this.customer.house_account_number &&
        this.customer.house_account_limit > 0
      ) {
        return true;
      } else {
        return false;
      }
    },
    houseAccountLimit() {
      return parseFloat(this.customer.house_account_limit);
    },
    amount: {
      get() {
        return this.paymentAmount.toFormat("0.00");
      },
      set(value) {
        this.paymentAmount = this.$price(value);
      }
    }
  },

  methods: {
    ...mapMutations("cart", ["setPaymentLoading"]),
    ...mapActions("cart", ["submitOrder"]),
    ...mapActions("requests", ["request"]),

    getPaymentTypes() {
      this.payment_types_loading = true;
      this.request({
        method: "get",
        url: "payment-types"
      })
        .then(response => {
          this.paymentTypes = response;
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.payment_types_loading = false;
        });
    },
    pay() {
      let payload;

      switch (this.paymentType) {
        case "pos-terminal":
          payload = {
            paymentAmount: this.paymentAmount,
            paymentType: this.paymentType
          };
          break;
        case "cash":
          payload = {
            paymentAmount: this.paymentAmount,
            paymentType: this.paymentType
          };
          break;
        case "card":
          payload = {
            card: this.card,
            paymentAmount: this.paymentAmount,
            paymentType: this.paymentType
          };
          break;
        case "house-account":
          payload = {
            house_account_number: this.houseAccountNumber,
            paymentAmount: this.paymentAmount,
            paymentType: this.paymentType
          };
          break;
        case "coupon":
          payload = {
            code: this.code,
            paymentType: this.paymentType
          };
          break;
        case "giftcard":
          payload = {
            code: this.code,
            paymentAmount: this.paymentAmount,
            paymentType: this.paymentType
          };
          break;
        default:
          break;
      }

      this.$emit("sendPayment", payload);
      this.max();
      this.orderLoading = false;
    },
    max() {
      if (this.paymentType !== "cash") {
        if (this.paymentType === "house-account") {
          if (this.houseAccountLimit.greaterThan(this.order_remaining_price)) {
            this.paymentAmount.equalsTo(this.order_remaining_price);
          } else if (this.paymentAmount.greaterThan(this.houseAccountLimit)) {
            this.paymentAmount.equalsTo(this.houseAccountLimit);
          }
        }
        if (this.paymentAmount.greaterThan(this.order_remaining_price)) {
          this.paymentAmount.equalsTo(this.order_remaining_price);
        }
      } else if (
        this.paymentAmount.greaterThan(this.$price({ amount: 999900 }))
      ) {
        this.amount = 999900;
      }
    },
    clearState() {
      if (process.env.NODE_ENV === "production") {
        this.code = null;
        this.card.number = null;
        this.card.card_holder = null;
        this.card.cvc = null;
        this.card.exp_date = null;
      }

      this.max();
    },
    sendPayment() {
      this.orderLoading = true;
      this.setPaymentLoading(true);

      if (!this.order_id) {
        this.submitOrder("create")
          .then(() => {
            this.pay();
          })
          .catch(error => {
            console.error(error);
          })
          .finally(() => {
            this.orderLoading = false;
          });
      } else {
        this.pay();
      }
    }
  }
};
</script>
