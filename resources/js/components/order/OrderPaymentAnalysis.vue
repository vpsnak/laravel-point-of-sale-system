<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left>mdi-file-chart</v-icon>
      <span class="subheading">
        Payment analysis
      </span>
    </v-card-title>
    <v-container>
      <v-row
        justify="space-around"
        align="center"
        dense
        v-for="(section, index) in costSections"
        :key="index"
      >
        <v-col :lg="4" :cols="6" class="text-right">
          <h4>
            {{ section.title }}
          </h4>
        </v-col>
        <v-col :lg="4" :cols="6">
          <h4>
            <i :class="section.class">{{ section.value }}</i>
          </h4>
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
      "order_total_price",
      "order_tax_price",
      "order_paid_price",
      "order_change_price",
      "order_remaining_price",
      "order_notes",
      "order_billing_address",
      "order_delivery_address",
      "order_delivery_store_pickup",
      "order_delivery_store_pickup",
      "shipping_cost",
      "payments"
    ]),

    costSections() {
      this.sections = [];

      this.payments.forEach(payment => {
        if (payment.status === "approved" && !payment.refunded) {
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
        } else if (payment.status === "refunded") {
          this.refund_total += Number(payment.amount);
        }
      });

      if (this.card_total > 0) {
        this.sections.push({
          title: "Credit Card (keyed)",
          value: this.card_total,
          class: "primary--text"
        });
      }
      if (this.pos_terminal_total > 0) {
        this.sections.push({
          title: "Credit Card (POS)",
          value: this.pos_terminal_total,
          class: "primary--text"
        });
      }
      if (this.cash_total > 0) {
        this.sections.push({
          title: "Cash",
          value: this.cash_total,
          class: "primary--text"
        });
      }
      if (this.house_account_total > 0) {
        this.sections.push({
          title: "House account",
          value: this.house_account_total,
          class: "primary--text"
        });
      }
      if (this.coupon_total > 0) {
        this.sections.push({
          title: "Coupons",
          value: this.coupon_total,
          class: "primary--text"
        });
      }
      if (this.giftcard_total > 0) {
        this.sections.push({
          title: "Giftcards",
          value: this.giftcard_total,
          class: "primary--text"
        });
      }
      if (this.refund_total < 0) {
        this.sections.push({
          title: "Refunds",
          value: this.refund_total * -1,
          class: "amber--text"
        });
      }
      this.sections.push({
        title: "Total paid",
        value: this.sumTotals >= 0 ? this.sumTotals : 0,
        class: "success--text"
      });

      return this.sections;
    },
    sumTotals() {
      const totals =
        Number(this.card_total) +
        Number(this.pos_terminal_total) +
        Number(this.cash_total) +
        Number(this.house_account_total) +
        Number(this.coupon_total) +
        Number(this.giftcard_total) +
        Number(this.refund_total);

      return totals;
    }
  }
};
</script>
