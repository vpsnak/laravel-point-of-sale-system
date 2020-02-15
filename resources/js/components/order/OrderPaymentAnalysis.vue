<template>
  <vc-donut
    hasLegend
    legendPlacement="right"
    :sections="costSections"
    :size="150"
    :thickness="13"
    :total="order_total"
    :background="bgColor"
  >
    <h2>$ {{ sumTotals }}</h2>
    <h2>total paid</h2>
  </vc-donut>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      sections: [],
      inc_refunds: false,

      card_total: 0,
      pos_terminal_total: 0,
      cash_total: 0,
      house_account_total: 0,
      giftcard_total: 0,
      coupon_total: 0,
      refund_total: 0
    };
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

    bgColor() {
      if (this.$vuetify.theme.dark) {
        return "#1e1e1e";
      }
    },

    costSections() {
      this.payments.forEach(payment => {
        if (!payment.refunded && payment.status === "approved") {
          switch (payment.payment_type.type) {
            case "pos-terminal":
              this.pos_terminal_total =
                Number(this.pos_terminal_total) + Number(payment.amount);
              break;
            case "card":
              this.card_total =
                Number(this.card_total) + Number(payment.amount);
              break;
            case "cash":
              this.cash_total =
                Number(this.cash_total) + Number(payment.amount);
              break;
            case "house-account":
              this.house_account_total =
                Number(this.house_account_total) + Number(payment.amount);
              break;
            case "coupon":
              this.coupon_total =
                Number(this.coupon_total) + Number(payment.amount);
              break;
            case "giftcard":
              this.giftcard_total =
                Number(this.giftcard_total) + Number(payment.amount);
              break;
          }
        } else {
          this.refund_total += Number(payment.amount);
        }
      });

      if (this.card_total > 0) {
        this.sections.push({
          label: `Credit Card API: $${this.card_total}`,
          value: this.card_total,
          color: "#003f5c"
        });
      }
      if (this.pos_terminal_total > 0) {
        this.sections.push({
          label: `Credit Card POS: $${this.pos_terminal_total}`,
          value: this.pos_terminal_total,
          color: "#374c80"
        });
      }
      if (this.cash_total > 0) {
        this.sections.push({
          label: `Cash: $${this.cash_total}`,
          value: this.cash_total,
          color: "#7a5195"
        });
      }
      if (this.house_account_total > 0) {
        this.sections.push({
          label: `House account: $${this.house_account_total}`,
          value: this.house_account_total,
          color: "#bc5090"
        });
      }
      if (this.coupon_total > 0) {
        this.sections.push({
          label: `Coupons: $${this.coupon_total}`,
          value: this.coupon_total,
          color: "#ef5675"
        });
      }
      if (this.giftcard_total > 0) {
        this.sections.push({
          label: `Giftcards: $${this.giftcard_total}`,
          value: this.giftcard_total,
          color: "#ff764a"
        });
      }
      if (this.refund_total > 0 && this.inc_refunds) {
        this.sections.push({
          label: `Refunds: $${this.refund_total}`,
          value: this.refund_total,
          color: "#ff764a"
        });
      }
      return this.sections;
    },
    sumTotals() {
      let totals =
        Number(this.card_total) +
        Number(this.pos_terminal_total) +
        Number(this.cash_total) +
        Number(this.house_account_total) +
        Number(this.coupon_total) +
        Number(this.giftcard_total);

      if (this.inc_refunds) {
        totals + Number(this.refund_total);
      }
      return totals;
    }
  }
};
</script>
