<template>
  <div v-if="remainingAmount > 0">
    <v-container fluid>
      <v-row dense justify="center" align="center">
        <v-col cols="12" justify="center" align="center">
          <h3 class="py-2">Methods</h3>

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
            :prepend-inner-icon="getIcon()"
            :disabled="loading || orderLoading"
            v-model="code"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" v-if="paymentType !== 'coupon'">
        <v-col :lg="2" :md="3">
          <v-text-field
            prepend-inner-icon="mdi-currency-usd"
            v-model="remainingAmount"
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
            @keyup="limits"
            @keyup.enter="sendPayment"
            @blur="limits"
          ></v-text-field>
        </v-col>
      </v-row>
    </v-container>

    <v-container fluid>
      <v-row justify="center" align="center" class="my-3" dense>
        <v-col lg="4" md="6">
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

<script>
import { mapActions, mapState } from "vuex";

export default {
  mounted() {
    this.getPaymentTypes();
  },

  beforeDestroy() {
    this.$off("sendPayment");
  },

  props: {
    remaining: String,
    loading: Boolean
  },

  data() {
    return {
      payment_types: [],
      orderLoading: false,
      paymentAmount: null,
      paymentType: null,
      code: null,

      card: {
        card_holder: null,
        number: null,
        cvc: null,
        exp_date: null
      }
    };
  },

  computed: {
    ...mapState("cart", ["order"]),

    paymentTypes: {
      get() {
        if (this.houseAccount) {
          return this.payment_types;
        } else {
          return _.filter(this.payment_types, function(o) {
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
        return this.$store.state.cart.customer.house_account_number;
      } else {
        return false;
      }
    },
    houseAccount() {
      if (this.$store.state.cart.customer) {
        if (
          this.$store.state.cart.customer.house_account_status &&
          this.$store.state.cart.customer.house_account_number &&
          this.$store.state.cart.customer.house_account_limit > 0
        ) {
          return true;
        }
      } else {
        return false;
      }
    },
    houseAccountLimit() {
      return parseFloat(this.$store.state.cart.customer.house_account_limit);
    },
    remainingAmount() {
      if (parseFloat(this.$props.remaining) >= 0) {
        this.amount = this.$props.remaining;
        return parseFloat(this.$props.remaining);
      } else {
        this.amount = parseFloat(this.$store.state.cart.cart_price).toFixed(2);
        return parseFloat(this.$store.state.cart.cart_price);
      }
    },
    amount: {
      get() {
        return this.paymentAmount;
      },
      set(value) {
        this.paymentAmount = value;
      }
    }
  },

  methods: {
    getPaymentTypes() {
      this.getAll({ model: "payment-types" }).then(response => {
        this.paymentTypes = response;
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
      this.limits();
    },
    limits() {
      if (this.paymentType !== "cash") {
        if (this.paymentType === "house-account") {
          if (this.houseAccountLimit > parseFloat(this.remainingAmount)) {
            this.amount = this.remainingAmount;
          } else if (
            parseFloat(this.amount) > parseFloat(this.houseAccountLimit)
          ) {
            this.amount = this.houseAccountLimit;
          }
        }
        if (parseFloat(this.amount) > parseFloat(this.remainingAmount)) {
          this.amount = this.remainingAmount.toFixed(2);
        }
      } else {
        if (parseFloat(this.amount) > 99999) {
          this.amount = 99999;
        }
      }
    },
    clearState() {
      this.code = null;
      this.card.number = null;
      this.card.card_holder = null;
      this.card.cvc = null;
      this.card.exp_date = null;

      this.limits();
    },
    sendPayment() {
      this.orderLoading = true;
      this.$store.state.cart.paymentLoading = true;

      if (!this.order.id) {
        this.submitOrder()
          .then(() => {
            this.$store.state.cart.products = [];
            this.pay();
          })
          .catch(error => {
            // unhandled error
            console.log(error.response);
          })
          .finally(() => {
            this.orderLoading = false;
          });
      } else {
        this.pay();
        this.orderLoading = false;
      }
    },

    ...mapActions("cart", ["submitOrder"]),
    ...mapActions(["getAll"]),

    getIcon() {
      return _.find(this.paymentTypes, ["type", this.paymentType]).icon;
    }
  }
};
</script>
