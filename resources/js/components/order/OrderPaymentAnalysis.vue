<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>mdi-chart-pie</v-icon>
      <span class="subheading">
        Payment Analysis
      </span>
    </v-card-title>
    <v-container>
      <v-row align="center" justify="center" no-gutters>
        <v-col>
          <vc-donut
            hasLegend
            legendPlacement="bottom"
            :sections="costSections"
            :size="150"
            :thickness="13"
            :total="order_total"
            background="#1e1e1e"
          >
            <h2>$ {{ sumTotals }}</h2>
            total paid
          </vc-donut>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      sections: [],
      exc_refunds: true,

      card_total: 0,
      pos_terminal_total: 0,
      cash_total: 0,
      house_account_total: 0,
      giftcard_total: 0,
      coupon_total: 0,
      refund_total: 0
    };
  },
  mounted() {
    this.sections = [
      {
        label: `credit card API: $${this.card_total}`,
        value: this.card_total,
        color: "#003f5c"
      },
      {
        label: `credit card POS: $${this.pos_terminal_total}`,
        value: this.pos_terminal_total,
        color: "#374c80"
      },
      {
        label: `cash: $${this.cash_total}`,
        value: this.cash_total,
        color: "#7a5195"
      },
      {
        label: `house account: $${this.house_account_total}`,
        value: this.house_account_total,
        color: "#bc5090"
      },
      {
        label: `coupon: $${this.coupon_total}`,
        value: this.coupon_total,
        color: "#ef5675"
      },
      {
        label: `gift card: $${this.giftcard_total}`,
        value: this.giftcard_total,
        color: "#ff764a"
      },
      {
        label: `refunds: $${this.refund_total}`,
        value: this.refund_total,
        color: "#ff764a"
      }
    ];
  },
  computed: {
    ...mapState("cart", [
      "order_id",
      "order_status",
      "order_total",
      "order_total_without_tax",
      "order_total_tax",
      "order_total_paid",
      "order_change",
      "order_remaining",
      "order_notes",
      "order_billing_address",
      "order_delivery_address",
      "order_delivery_store_pickup",
      "order_delivery_store_pickup",
      "shipping_cost",
      "payments"
    ]),

    costSections() {
      this.payments.forEach(payment => {
        if (!payment.refunded) {
          switch (payment.payment_type.type) {
            case "pos-terminal":
              this.pos_terminal_total += parseFloat(payment.amount);
              break;
            case "card":
              this.card_total += parseFloat(payment.amount);
              break;
            case "cash":
              this.cash_total += parseFloat(payment.amount);
              break;
            case "house-account":
              this.house_account_total += parseFloat(payment.amount);
              break;
            case "coupon":
              this.coupon_total += parseFloat(payment.amount);
              break;
            case "giftcard":
              this.giftcard_total += parseFloat(payment.amount);
              break;
          }
        } else {
          refund_total += parseFloat(payment.amount);
        }
      });

      if (this.card_total > 0) {
        this.sections.push({
          label: `Credit Card API: $${this.card_total}`,
          value: this.card_total,
          color: "gray"
        });
      }

      if (this.pos_terminal_total > 0) {
        this.sections.push({
          label: `Credit Card POS: $${this.pos_terminal_total}`,
          value: this.pos_terminal_total,
          color: "gray"
        });
      }
      if (this.cash_total > 0) {
        this.sections.push({
          label: `Cash: $${this.cash_total}`,
          value: this.cash_total,
          color: "purple"
        });
      }
      if (this.house_account_total > 0) {
        this.sections.push({
          label: `House account: $${this.house_account_total}`,
          value: this.house_account_total,
          color: "purple"
        });
      }
      if (this.coupon_total > 0) {
        this.sections.push({
          label: `Cash: $${this.coupon_total}`,
          value: this.coupon_total,
          color: "purple"
        });
      }
      if (this.giftcard_total > 0) {
        this.sections.push({
          label: `Cash: $${this.giftcard_total}`,
          value: this.giftcard_total,
          color: "purple"
        });
      }
      return this.sections;
    },
    sumTotals() {
      let totals =
        parseFloat(this.card_total) +
        parseFloat(this.pos_terminal_total) +
        parseFloat(this.cash_total) +
        parseFloat(this.house_account_total) +
        parseFloat(this.coupon_total) +
        parseFloat(this.giftcard_total);

      !this.exc_refunds ? totals + parseFloat(this.refund_total) : "";
      return totals;
    }
  }
};
</script>
