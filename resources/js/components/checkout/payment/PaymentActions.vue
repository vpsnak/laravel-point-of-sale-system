<template>
  <ValidationObserver
    tag="v-form"
    @submit.prevent="sendPayment()"
    v-slot="{ invalid }"
  >
    <v-container fluid>
      <v-row v-if="paymentTypesLoading" no-gutters justify="center">
        <v-skeleton-loader
          v-for="n in 5"
          :key="n"
          type="chip"
          class="mx-2"
          tile
        />
      </v-row>
      <v-row v-else no-gutters justify="center" align="center">
        <v-chip-group
          mandatory
          show-arrows
          v-model="paymentType"
          @change="clearState()"
        >
          <v-chip
            v-for="payment_type in paymentTypes"
            :key="payment_type.id"
            :value="payment_type"
            :disabled="loading"
            outlined
            active-class="primary--text"
            label
          >
            <v-icon left v-text="payment_type.icon" />
            <span v-text="payment_type.name" />
          </v-chip>
        </v-chip-group>
      </v-row>
    </v-container>
    <v-divider class="mb-2" />
    <v-container fluid class="overflow-y-auto py-0" style="max-height: 75px;">
      <v-row
        justify="center"
        align="center"
        v-if="paymentType.type === 'card'"
        dense
      >
        <v-col :lg="3" :cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Card number"
          >
            <v-text-field
              dense
              outlined
              type="number"
              autocomplete="off"
              label="Card number"
              prepend-inner-icon="mdi-credit-card"
              v-model="card.number"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :lg="3" :cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Card holder's name"
          >
            <v-text-field
              dense
              outlined
              autocomplete="off"
              label="Card holder's name"
              prepend-inner-icon="mdi-account-box"
              v-model="card.card_holder"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :lg="2" :cols="6">
          <ValidationProvider
            rules="required|digits:4"
            v-slot="{ errors }"
            name="Exp date"
          >
            <v-text-field
              dense
              outlined
              type="number"
              autocomplete="off"
              :disabled="loading"
              label="Exp date"
              persistent-hint
              v-model="card.exp_date"
              prepend-inner-icon="mdi-calendar"
              :error-messages="errors[0] ? 'Format: MMYY' : null"
              hint="Format: MMYY"
            />
          </ValidationProvider>
        </v-col>
        ​
        <v-col :lg="2" :cols="6">
          <ValidationProvider
            rules="required|min:3|max:4"
            v-slot="{ errors }"
            name="CVC/CVV"
          >
            <v-text-field
              dense
              outlined
              autocomplete="off"
              label="CVC/CVV"
              type="number"
              prepend-inner-icon="mdi-lock"
              v-model="card.cvc"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row
        dense
        justify="center"
        align="center"
        v-else-if="['giftcard', 'coupon'].indexOf(paymentType.type) !== -1"
      >
        <v-col :lg="2" :md="3" :cols="6">
          <ValidationProvider rules="required" v-slot="{ errors }" name="Code">
            <v-text-field
              dense
              outlined
              label="Code"
              :prepend-inner-icon="paymentType.icon"
              :disabled="loading"
              v-model="code"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
      </v-row>
    </v-container>

    <v-container fluid>
      <v-row justify="center" dense>
        <v-col :lg="2" :md="3" :cols="6">
          <v-text-field
            dense
            outlined
            :value="orderRemainingPrice.toFormat()"
            disabled
            label="Remaining Amount"
            :hint="hintSpacing"
          />
        </v-col>
        <v-col :lg="2" :md="3" :cols="6" v-if="paymentType.type !== 'coupon'">
          <ValidationProvider
            :rules="`required|between:0.01,${amountRules}`"
            v-slot="{ errors }"
            name="Payment amount"
          >
            <v-text-field
              dense
              outlined
              :disabled="loading"
              :min="0.01"
              :max="amountRules"
              label="Payment amount"
              type="number"
              prefix="$"
              v-model="amount"
              :error-messages="amountRules > 0 ? errors : 'Discount error'"
            />
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" dense>
        <v-col :lg="4" :cols="6">
          <v-btn
            outlined
            type="submit"
            dark
            block
            color="primary"
            :loading="makePaymentLoading"
            :disabled="invalid || !isValidCheckout || loading"
            v-text="'Make a payment'"
          />
        </v-col>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>
​
<script>
import { mapActions, mapState, mapMutations } from "vuex";

export default {
  created() {
    this.getPaymentTypes();
    this.fillDemoCard();
  },

  watch: {
    order_remaining_price: {
      immediate: true,
      handler(value) {
        if (!this.order_status || this.order_status.can_checkout) {
          value = this.parsePrice(value);
          if (value.isPositive() || value.isZero()) {
            this.paymentPrice = value;
          } else {
            this.paymentPrice = this.parsePrice();
          }
          this.setAmount();
        }
      },
    },
  },

  data() {
    return {
      makePaymentLoading: false,
      amount_value: null,
      paymentTypesLoading: false,
      payment_types: [],
      paymentPrice: null,
      paymentType: {},
      code: null,

      card: {
        card_holder: null,
        number: null,
        cvc: null,
        exp_date: null,
      },
    };
  },

  computed: {
    ...mapState("cart", [
      "order_remaining_price",
      "order_id",
      "order_total_price",
      "order_status",
      "customer",
      "checkout_loading",
      "isValidCheckout",
    ]),

    loading() {
      if (
        this.checkout_loading ||
        this.makePaymentLoading ||
        this.paymentTypesLoading
      ) {
        return true;
      } else {
        return false;
      }
    },
    amount: {
      get() {
        return this.amount_value;
      },
      set(value) {
        this.amount_value = value;
        this.paymentPrice = this.parsePrice(Math.round(value * 10000) / 100);
      },
    },
    orderRemainingPrice() {
      if (
        this.order_remaining_price &&
        _.has(this.order_remaining_price, "amount") &&
        this.order_remaining_price.amount > 0
      ) {
        return this.parsePrice(this.order_remaining_price);
      } else {
        return this.parsePrice();
      }
    },
    amountRules() {
      if (this.orderRemainingPrice.isZero()) {
        return -1;
      } else {
        switch (this.paymentType.type) {
          case "card":
          case "house-account":
          case "giftcard":
          case "pos-terminal":
            return this.orderRemainingPrice.toFormat("0.00");
          case "cash":
            return "10000.00";
        }
      }
    },
    paymentTypes() {
      if (this.houseAccount) {
        return this.payment_types;
      } else {
        return _.filter(this.payment_types, (o) => {
          return o.type !== "house-account";
        });
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
  },

  methods: {
    ...mapMutations("cart", [
      "setOrderId",
      "setTransactions",
      "setOrderChangePrice",
      "setOrderRemainingPrice",
      "setOrderStatus",
      "setCheckoutLoading",
    ]),
    ...mapActions("cart", ["submitOrder"]),
    ...mapActions("requests", ["request"]),

    setAmount() {
      this.amount = this.orderRemainingPrice.toFormat("0.00");
    },
    getPaymentTypes() {
      this.paymentTypesLoading = true;
      this.setCheckoutLoading(true);
      this.request({
        method: "get",
        url: "payment-types",
      })
        .then((response) => {
          this.payment_types = response;
        })
        .catch((error) => {
          console.error(error);
        })
        .finally(() => {
          this.paymentTypesLoading = false;
          this.setCheckoutLoading(false);
        });
    },
    pay() {
      this.makePaymentLoading = true;
      this.setCheckoutLoading(true);
      let data = {
        order_id: this.order_id,
        payment_type_id: this.paymentType.id,
        payment_type: this.paymentType.type,
      };

      switch (this.paymentType.type) {
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
      }

      const payload = {
        method: "post",
        url: "payments/create",
        data: data,
      };

      this.request(payload)
        .then((response) => {
          this.setOrderChangePrice(response.transaction.payment.change_price);
          this.setOrderRemainingPrice(response.remaining);
          this.setOrderStatus(response.status);

          this.setTransactions(response.transaction);
        })
        .catch((error) => {
          console.error(error);
          if (_.has(error, "transaction")) {
            this.setTransactions(error.transaction);
          }
        })
        .finally(() => {
          this.makePaymentLoading = false;
          this.setCheckoutLoading(false);
        });
    },
    fillDemoCard() {
      if (process.env.NODE_ENV === "development") {
        this.card = {
          card_holder: "John Longjohn",
          number: "4000000000000002",
          cvc: "123",
          exp_date: "1224",
        };
      } else {
        this.card.card_holder = null;
        this.card.number = null;
        this.card.cvc = null;
        this.card.exp_date = null;
      }
    },
    clearState() {
      this.fillDemoCard();
      this.code = null;
    },
    sendPayment() {
      if (!this.order_id) {
        this.makePaymentLoading = true;
        this.setCheckoutLoading(true);
        this.submitOrder("create")
          .then((response) => {
            this.setOrderId(response.id);
            this.pay();
          })
          .catch((error) => {
            console.error(error);
            this.setCheckoutLoading(false);
            this.makePaymentLoading = false;
          });
      } else {
        this.pay();
      }
    },
  },
};
</script>
