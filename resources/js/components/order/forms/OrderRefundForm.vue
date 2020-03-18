<template>
  <ValidationObserver
    ref="refund-form"
    v-slot="{ valid }"
    tag="v-form"
    @submit.prevent="submitRefund()"
  >
    <v-card :width="300">
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>
              Refundable amount:
              <b class="primary--text">{{ maxRefundablePrice.toFormat() }}</b>
            </v-list-item-title>
            <v-list-item-subtitle>
              <v-icon small>{{ subtitle.icon }}</v-icon>
              <span v-html="subtitle.txt" />
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-container>
        <v-row justify="center">
          <v-col :cols="6">
            <v-text-field
              type="number"
              v-model="amount"
              :max="maxRefundablePrice.toFormat(0.0)"
              prefix="$"
              label="Amount"
              single-line
              dense
            ></v-text-field>
          </v-col> </v-row
      ></v-container>

      <v-card-actions>
        <v-spacer />

        <v-btn color="primary" text type="submit" :disabled="!valid">
          Refund
        </v-btn>
        <v-spacer />
      </v-card-actions>
    </v-card>
  </ValidationObserver>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    transaction: Object
  },

  created() {
    this.maxRefundablePrice = this.parsePrice(
      this.$props.transaction.payment.refundable_price
    );

    this.price.currency = this.$props.transaction.price.currency;
  },

  beforeDestroy() {
    EventBus.$off("");
  },

  data() {
    return {
      display_amount: null,
      maxRefundablePrice: null,
      price: {
        amount: null,
        currency: null
      }
    };
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "order_income_price",
      "order_total_price",
      "order_status"
    ]),

    amount: {
      get() {
        return this.display_amount;
      },
      set(value) {
        this.display_amount = value;
        this.price.amount = Math.round(value * 10000) / 100;
      }
    },
    subtitle() {
      switch (this.$props.transaction.payment.payment_type.type) {
        case "card":
          return {
            icon: "mdi-credit-card-outline",
            txt: `<b>${this.$props.transaction.last_log.card_number}</b> / <b>${this.$props.transaction.last_log.card_holder}</b>`
          };
        case "pos-terminal":
          return {
            icon: "mdi-credit-card-outline",
            txt: ``
          };
          break;
        case "giftcard":
          return {
            icon: "mdi-wallet-giftcard",
            txt: `<b>${this.$props.transaction.code}</b>`
          };
          break;
        case "coupon":
          return {
            icon: "mdi-ticket-percent",
            txt: `<b>${this.$props.transaction.code}</b>`
          };
          break;
        default:
          return {};
      }
      return;
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("requests", ["request"]),

    submitRefund() {
      this.loading = true;

      const payload = {
        method: "post",
        url: "refunds/create",
        mutations: [],
        data: {
          order_id: this.order_id,
          payment_id: this.$props.transaction.payment.id,
          price: this.parsePrice(this.price).toJSON()
        }
      };

      this.request(payload)
        .then(response => {})
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
