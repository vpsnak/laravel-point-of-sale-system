<template>
  <ValidationObserver
    tag="v-form"
    @submit.prevent="sendPayment()"
    v-slot="{ invalid }"
  >
    <v-container fluid>
      <v-row dense justify="center" align="center">
        <v-col :cols="12" justify="center" align="center">
          <h3 class="py-2">Methods</h3>
          ​<v-progress-circular
            v-if="payment_types_loading"
            indeterminate
            color="secondary"
          ></v-progress-circular>
          <v-btn-toggle
            v-model="paymentType"
            mandatory
            @change="clearState"
            dense
          >
            <v-btn
              v-for="paymentType in paymentTypes"
              :key="paymentType.id"
              :value="paymentType.type"
              :disabled="loading || orderLoading"
              dense
            >
              <v-icon class="pr-2" small>{{ paymentType.icon }}</v-icon>
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
        v-else-if="['giftcard', 'coupon'].indexOf(paymentType) !== -1"
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
          <ValidationProvider
            :rules="`required|between:0.01,${amountRules}`"
            v-slot="{ errors, valid }"
            name="Payment amount"
          >
            <v-text-field
              :disabled="loading || orderLoading"
              :min="0.01"
              :max="amountRules"
              label="Payment amount"
              type="number"
              prepend-inner-icon="mdi-currency-usd"
              v-model="amount"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
    </v-container>
    ​
    <v-container fluid>
      <v-row justify="center" align="center" class="my-3" dense>
        <v-col :lg="4" :md="6">
          <v-btn
            type="submit"
            dark
            block
            color="green darken-3"
            :loading="loading || orderLoading"
            :disabled="
              invalid ||
                loading ||
                orderLoading ||
                !$store.state.cart.isValidCheckout
            "
          >
            Make a payment
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>
​
<script>
import { mapActions, mapState, mapMutations } from "vuex";

export default {
  props: {
    loading: Boolean
  },

  created() {
    this.amount = this.order_remaining_price.toFormat("0.00");
  },

  mounted() {
    this.getPaymentTypes();
    this.fillDemoCard();
    if (process.env.NODE_ENV === "development") {
      this.card = {
        card_holder: "John Longjohn",
        number: "4000000000000002",
        cvc: "123",
        exp_date: "1224"
      };
    }
  },

  beforeDestroy() {
    this.$off("sendPayment");
  },

  watch: {
    order_remaining_price(value) {
      if (this.order_status === "pending_payment" || !this.order_status) {
        this.paymentAmount = value;
      }
    }
  },

  data() {
    return {
      amount_value: null,
      payment_types_loading: false,
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
    ...mapState("cart", [
      "order_remaining_price",
      "order_id",
      "order_total_price",
      "order_status",
      "customer"
    ]),

    amount: {
      get() {
        return this.amount_value;
      },
      set(value) {
        this.amount_value = value;
        this.paymentAmount = this.parsePrice(value);
      }
    },
    amountRules() {
      switch (this.paymentType) {
        case "card":
        case "house-account":
        case "giftcard":
        case "pos-terminal":
          return this.order_remaining_price.toFormat("0.00");
        case "cash":
          return "10000.00";
      }
    },
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
      let payload = {
        paymentType: this.paymentType
      };

      switch (this.paymentType) {
        case "pos-terminal":
        case "cash":
          payload.paymentAmount = this.paymentAmount;
          break;
        case "card":
          payload.card = this.card;
          paymentAmount = this.paymentAmount;
          break;
        case "house-account":
          payload.house_account_number = this.houseAccountNumber;
          payload.paymentAmount = this.paymentAmount;
          break;
        case "coupon":
          payload.code = this.code;
          break;
        case "giftcard":
          payload.code = this.code;
          payload.code = this.paymentAmount;
          break;
        default:
          return;
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
    fillDemoCard() {
      if (process.env.NODE_ENV === "development") {
        this.card = {
          card_holder: "John Longjohn",
          number: "4000000000000002",
          cvc: "123",
          exp_date: "1224"
        };
      }
    },
    clearState() {
      this.fillDemoCard();
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
