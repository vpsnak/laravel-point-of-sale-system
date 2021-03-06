<template>
  <ValidationObserver
    ref="refund-form"
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submitRefund()"
  >
    <v-card :width="300">
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="'Refundable amount:'" />
              <b class="primary--text" v-text="maxRefundablePrice.toFormat()" />
            </v-list-item-title>
            <v-list-item-subtitle>
              <v-icon small v-text="subtitle.icon" />
              <span v-html="`<b>${subtitle.txt}</b>`" />
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-divider />

      <v-container>
        <v-row justify="center">
          <v-col :lg="4" :cols="6">
            <ValidationProvider
              :rules="`required|between:0.01,${maxRefundablePrice.toFormat(
                '0.00'
              )}`"
              v-slot="{ invalid }"
              name="Amount"
            >
              <v-text-field
                autofocus
                class="text-center"
                type="number"
                v-model="amount"
                :max="maxRefundablePrice.toFormat(0.0)"
                min="0.01"
                prefix="$"
                label="Amount"
                single-line
                dense
                :disabled="disableAmount || loading"
                :error="invalid"
              />
            </ValidationProvider>
          </v-col>
        </v-row>
      </v-container>

      <v-card-actions class="justify-center">
        <v-btn
          color="primary"
          text
          outlined
          type="submit"
          :loading="loading"
          :disabled="invalid || loading"
        >
          Refund
        </v-btn>
      </v-card-actions>
    </v-card>
  </ValidationObserver>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    transaction: Object,
  },

  created() {
    this.maxRefundablePrice = this.parsePrice(
      this.$props.transaction.payment.refundable_price
    );
    this.price.currency = this.$props.transaction.price.currency;
  },

  beforeDestroy() {
    this.$off("submit");
  },

  data() {
    return {
      loading: false,
      display_amount: null,
      maxRefundablePrice: null,
      price: {
        amount: null,
        currency: null,
      },
    };
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "order_income_price",
      "order_total_price",
      "order_status",
      "transactions",
    ]),

    disableAmount() {
      switch (this.$props.transaction.payment.payment_type.type) {
        case "card":
        case "pos-terminal":
          this.price = this.maxRefundablePrice.toJSON();
          this.display_amount = this.maxRefundablePrice.toFormat("0.00");
          return true;
        default:
          return false;
      }
    },
    amount: {
      get() {
        return this.display_amount;
      },
      set(value) {
        this.display_amount = value;
        this.price.amount = Math.round(value * 10000) / 100;
      },
    },
    subtitle() {
      switch (this.$props.transaction.payment.payment_type.type) {
        case "card":
          return {
            icon: "mdi-credit-card-outline",
            txt: `${this.$props.transaction.last_log.card_number} / ${this.$props.transaction.last_log.card_holder}`,
          };
        case "pos-terminal":
          return {
            icon: "mdi-credit-card-outline",
            txt: ``,
          };
        case "giftcard":
          return {
            icon: "mdi-wallet-giftcard",
            txt: this.$props.transaction.code,
          };
        case "coupon":
          return {
            icon: "mdi-ticket-percent",
            txt: this.$props.transaction.code,
          };
        case "cash":
          return {
            icon: "mdi-cash-multiple",
            txt: `Cash`,
          };
        default:
          return {};
      }
      return;
    },
  },

  methods: {
    ...mapMutations("cart", [
      "setCheckoutLoading",
      "setTransactions",
      "setOrderStatus",
    ]),
    ...mapActions("requests", ["request"]),

    submitRefund() {
      this.setCheckoutLoading(true);
      this.loading = true;

      const payload = {
        method: "post",
        url: "refunds/create",
        mutations: [],
        data: {
          order_id: this.order_id,
          payment_id: this.$props.transaction.payment.id,
          price: this.parsePrice(this.price).toJSON(),
        },
      };

      this.request(payload)
        .then((response) => {
          EventBus.$emit("income-analysis-refresh");

          this.setTransactions(response.transaction);
          this.setOrderStatus(response.status);

          this.maxRefundablePrice = this.parsePrice(
            response.refunded_transaction.payment.refundable_price
          );

          this.$emit("submit");
        })
        .catch((error) => {
          console.log(error);
          if (_.has(error, "transaction"))
            this.setTransactions(error.transaction);
        })
        .finally(() => {
          this.amount = null;
          this.loading = false;
          this.setCheckoutLoading(false);
        });
    },
  },
};
</script>
