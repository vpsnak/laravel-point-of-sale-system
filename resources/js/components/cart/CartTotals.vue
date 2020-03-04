<template>
  <div class="d-flex flex-column">
    <div
      class="d-flex justify-space-between pa-2"
      v-if="!totalDiscount.isZero()"
    >
      <span>Total discount</span>
      <span>{{ totalDiscount.toFormat("$0,0.00") }}</span>
    </div>

    <v-divider v-if="!totalDiscount.isZero()" />

    <div class="d-flex justify-space-between pa-2">
      <span>Sub total w/ discount</span>
      <span>{{ subTotalwDiscount.toFormat("$0,0.00") }}</span>
    </div>

    <v-divider />

    <div
      class="d-flex justify-space-between pa-2"
      v-if="!deliveryFeesPrice.isZero()"
    >
      <span>Delivery Fees</span>
      <span>{{ deliveryFeesPrice.toFormat("$0,0.00") }}</span>
    </div>

    <v-divider v-if="!deliveryFeesPrice.isZero()" />

    <div class="d-flex justify-space-between pa-2 bb-1">
      <span>Tax</span>
      <span>{{ tax.toFormat("$0,0.00") }}</span>
    </div>

    <v-divider />

    <div class="d-flex justify-space-between pa-2">
      <span>Total</span>
      <span>{{ orderTotal.toFormat("$0,0.00") }}</span>
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
      "order_discount",
      "delivery_fees_price",
      "order_status",
      "order_total",
      "order_total_without_tax",
      "order_total_tax"
    ]),

    deliveryFeesPrice() {
      return this.$price(this.delivery_fees_price);
    },
    subTotalwDiscount() {
      var subtotal = this.$price();

      this.cart_products.forEach(product => {
        const productPrice = this.$price(product.price).multiply(
          Number(product.qty)
        );
        const result = this.calcDiscount(productPrice, product.discount);
        if (result.isZero()) {
          subtotal = subtotal.add(productPrice);
        } else {
          subtotal = subtotal.add(result);
        }
      });
      const cartDiscount = this.calcDiscount(subtotal, this.order_discount);
      return subtotal.subtract(cartDiscount);
    },
    tax() {
      if (this.customer && this.customer.no_tax) {
        return this.$price();
      } else {
        return this.subTotalwDiscount
          .add(this.deliveryFeesPrice)
          .percentage(this.tax_percentage);
      }
    },
    totalDiscount() {
      var subtotalNoDiscount = this.$price();

      this.cart_products.forEach(product => {
        const result = this.$price(product.price).multiply(Number(product.qty));
        subtotalNoDiscount = subtotalNoDiscount.add(result);
      });
      this.isValidDiscount();

      return subtotalNoDiscount.subtract(this.subTotalwDiscount);
    },
    orderTotal() {
      return this.subTotalwDiscount.add(this.tax);
    }
  },
  methods: {
    ...mapMutations("cart", ["setOrderTotal", "isValidDiscount"]),

    calcDiscount(price, discount) {
      if (discount && _.has(discount, "price") && _.has(discount, "type")) {
        switch (_.lowerCase(type)) {
          case "flat":
            return price.subtract(amount);
          case "percentage":
            return price.percentage(Number(amount)).subtract(price);
          default:
            return this.$price();
        }
      } else {
        return this.$price();
      }
    }
  }
};
</script>
