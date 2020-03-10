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
            v-model="payment_type"
            mandatory
            @change="clearState"
            dense
          >
            <v-btn
              v-for="payment_type in paymentTypes"
              :key="payment_type.id"
              :value="payment_type.type"
              :disabled="false"
              dense
            >
              <v-icon class="pr-2" small>{{ payment_type.icon }}</v-icon>
              {{ payment_type.name }}
            </v-btn>
          </v-btn-toggle>
        </v-col>
      </v-row>
    </v-container>
    <v-container fluid class="overflow-y-auto" style="max-height: 20vh">
      <v-row justify="center" align="center" v-if="payment_type === 'card'">
        <v-col :lg="3" :md="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Card number"
          >
            <v-text-field
              dense
              type="number"
              autocomplete="off"
              label="Card number"
              prepend-inner-icon="mdi-credit-card"
              v-model="card.number"
              :disabled="false"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col :lg="3" :md="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Card holder's name"
          >
            <v-text-field
              dense
              autocomplete="off"
              label="Card holder's name"
              prepend-inner-icon="mdi-account-box"
              v-model="card.card_holder"
              :disabled="false"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" v-if="payment_type === 'card'">
        <v-col :lg="3" :md="6">
          <ValidationProvider
            rules="required|digits:4"
            v-slot="{ errors, valid }"
            name="Exp date"
          >
            <v-text-field
              dense
              type="number"
              autocomplete="off"
              :disabled="false"
              label="Exp date"
              v-model="card.exp_date"
              prepend-inner-icon="mdi-calendar"
              :error-messages="errors[0] ? 'Format: MMYY' : null"
              hint="Format: MMYY"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        ​
        <v-col :lg="3" :md="6">
          <ValidationProvider
            rules="required|min:3|max:4"
            v-slot="{ errors, valid }"
            name="CVC/CVV"
          >
            <v-text-field
              dense
              autocomplete="off"
              label="CVC/CVV"
              type="number"
              prepend-inner-icon="mdi-lock"
              v-model="card.cvc"
              :disabled="false"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row
        justify="center"
        align="center"
        v-else-if="['giftcard', 'coupon'].indexOf(payment_type) !== -1"
      >
        <v-col :lg="3" :md="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Code"
          >
            <v-text-field
              dense
              label="Code"
              :prepend-inner-icon="getIcon"
              :disabled="false"
              v-model="code"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" v-if="payment_type !== 'coupon'">
        <v-col :lg="2" :md="3">
          <v-text-field
            prepend-inner-icon="mdi-currency-usd"
            :value="orderRemainingPrice.toFormat('0.00')"
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
              :disabled="false"
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
            :loading="false"
            :disabled="invalid || !$store.state.cart.isValidCheckout"
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

  watch: {
    order_remaining_price: {
      immediate: true,
      handler(value) {
        if (this.order_status === "pending_payment" || !this.order_status) {
          this.paymentPrice = this.parsePrice(value);
          this.setAmount();
        }
      }
    }
  },

  data() {
    return {
      amount_value: null,
      payment_types_loading: false,
      payment_types: [],
      paymentPrice: null,
      payment_type: null,
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
        this.paymentPrice = this.parsePrice({
          amount: Math.round(value * 10000) / 100
        });
      }
    },
    orderRemainingPrice() {
      if (
        this.order_remaining_price &&
        _.has(this.order_remaining_price, "amount")
      ) {
        return this.$price(this.order_remaining_price);
      } else {
        return this.$price();
      }
    },
    amountRules() {
      switch (this.payment_type) {
        case "card":
        case "house-account":
        case "giftcard":
        case "pos-terminal":
          return this.orderRemainingPrice.toFormat("0.00");
        case "cash":
          return "10000.00";
      }
    },
    getIcon() {
      return _.find(this.paymentTypes, ["type", this.payment_type]).icon;
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
    ...mapMutations("cart", [
      "setOrderId",
      "setPayments",
      "setOrderChangePrice",
      "setOrderRemainingPrice",
      "setOrderStatus"
    ]),
    ...mapActions("cart", ["submitOrder"]),
    ...mapActions("requests", ["request"]),

    setAmount() {
      console.log(this.orderRemainingPrice);
      this.amount = this.orderRemainingPrice.toFormat("0.00");
    },
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
      let data = {
        order_id: this.order_id,
        payment_type: this.payment_type
      };

      switch (this.payment_type) {
        case "pos-terminal":
        case "cash":
          data.price = this.paymentPrice.toJSON();
          break;
        case "card":
          data.card = this.card;
          data.price = this.paymentPrice.toJSON();
          break;
        case "house-account":
          data.house_account_number = this.houseAccountNumber;
          data.price = this.paymentPrice.toJSON();
          break;
        case "coupon":
          data.code = this.code;
          break;
        case "giftcard":
          data.code = this.code;
          data.price = this.paymentPrice.toJSON();
          break;
        default:
          return;
      }

      const payload = {
        method: "post",
        url: "payments/create",
        data: data
      };

      this.request(payload)
        .then(response => {
          this.setOrderChangePrice(response.payment.change_price);
          this.setOrderRemainingPrice(response.remaining);
          this.setOrderStatus(response.status);

          this.setPayments(response.payment);
        })
        .catch(error => {
          console.error(error);
          if (_.has(error, "payment")) {
            this.setPayments(error.payment);
          }
        })
        .finally(() => {});
    },
    max() {
      if (this.payment_type !== "cash") {
        if (this.payment_type === "house-account") {
          if (this.houseAccountLimit.greaterThan(this.orderRemainingPrice)) {
            this.paymentPrice.equalsTo(this.orderRemainingPrice);
          } else if (this.paymentPrice.greaterThan(this.houseAccountLimit)) {
            this.paymentPrice.equalsTo(this.houseAccountLimit);
          }
        }
        if (this.paymentPrice.greaterThan(this.orderRemainingPrice)) {
          this.paymentPrice.equalsTo(this.orderRemainingPrice);
        }
      } else if (
        this.paymentPrice.greaterThan(this.$price({ amount: 999900 }))
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
      if (!this.order_id) {
        this.submitOrder("create")
          .then(response => {
            console.log(response);
            this.setOrderId(response.id);
            this.pay();
          })
          .catch(error => {
            console.error(error);
          })
          .finally(() => {});
      } else {
        this.pay();
      }
    }
  }
};
</script>
