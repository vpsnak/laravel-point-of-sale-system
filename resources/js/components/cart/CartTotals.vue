<template>
  <div class="d-flex flex-column">
    <div class="d-flex justify-space-between pa-2" v-if="totalDiscount">
      <span>Total discount</span>
      <span>$ {{ totalDiscount.toFixed(2) }}</span>
    </div>

    <v-divider v-if="totalDiscount" />

    <div class="d-flex justify-space-between pa-2">
      <span>Sub total w/ discount</span>
      <span>$ {{ subTotalwDiscount.toFixed(2) }}</span>
    </div>

    <v-divider />

    <div class="d-flex justify-space-between pa-2" v-if="shippingCost">
      <span>Delivery Fees</span>
      <span>$ {{ shippingCost.toFixed(2) }}</span>
    </div>

    <v-divider v-if="shippingCost" />

    <div class="d-flex justify-space-between pa-2 bb-1">
      <span>Tax</span>
      <span>$ {{ tax.toFixed(2) }}</span>
    </div>

    <v-divider />

    <div class="d-flex justify-space-between pa-2">
      <span>Total</span>
      <span>$ {{ order_total.toFixed(2) }}</span>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  computed: {
    ...mapState("cart", [
      "tax_percentage",
      "order_id",
      "cart_products",
      "customer",
      "order_total",
      "discount_type",
      "discount_amount",
      "shipping_cost",
      "order_status",
      "order_total",
      "order_total_without_tax",
      "order_total_tax"
    ]),

    shippingCost() {
      if (this.shipping_cost) {
        return parseFloat(this.shipping_cost);
      } else {
        return 0;
      }
    },
    subTotalwDiscount() {
      let subtotal = 0;

      this.cart_products.forEach(product => {
        subtotal += this.calcDiscount(
          product.final_price * product.qty,
          product.discount_type,
          product.discount_amount
        );
      });

      if (this.discount_type && this.discount_amount > 0) {
        subtotal -=
          subtotal -
          this.calcDiscount(subtotal, this.discount_type, this.discount_amount);
      }

      return parseFloat(subtotal);
    },
    tax() {
      if (this.customer && this.customer.no_tax) {
        return 0;
      } else {
        return parseFloat(
          ((this.subTotalwDiscount + this.shippingCost) *
            parseFloat(this.tax_percentage)) /
            100
        );
      }
    },
    totalDiscount() {
      let subtotalNoDiscount = 0;

      this.cart_products.forEach(product => {
        subtotalNoDiscount += product.final_price * product.qty;
      });

      this.setOrderTotal(this.subTotalwDiscount + this.tax + this.shippingCost);

      this.isValidDiscount();

      return subtotalNoDiscount - this.subTotalwDiscount;
    }
  },
  methods: {
    ...mapMutations("cart", ["setOrderTotal", "isValidDiscount"]),

    calcDiscount(price, type, amount) {
      if (type && amount) {
        switch (_.lowerCase(type)) {
          case "flat":
            return parseFloat(price).toFixed(2) - parseFloat(amount).toFixed(2);
          case "percentage":
            return (
              parseFloat(price) - (parseFloat(price) * parseFloat(amount)) / 100
            );
          default:
            return parseFloat(price);
        }
      }
      return parseFloat(price);
    }
  }
};
</script>
