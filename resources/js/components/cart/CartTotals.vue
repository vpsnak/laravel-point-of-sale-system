<template>
  <v-container>
    <v-row v-if="!totalDiscount.isZero()" class="pa-1" dense>
      <span>Total discount</span>
      <v-spacer />
      <span>{{ totalDiscount.toFormat() }}</span>
    </v-row>

    <v-divider v-if="!totalDiscount.isZero()" />

    <v-row class="pa-1" dense>
      <span>Sub total w/ discount</span>
      <v-spacer />
      <span>{{ subTotalwDiscount.toFormat() }}</span>
    </v-row>

    <v-divider />

    <v-row class="pa-1" v-if="!deliveryFeesPrice.isZero()" dense>
      <span>Delivery Fees</span>
      <v-spacer />
      <span>{{ deliveryFeesPrice.toFormat() }}</span>
    </v-row>

    <v-divider v-if="!deliveryFeesPrice.isZero()" />

    <v-row class="pa-1" dense>
      <span>Tax</span>
      <v-spacer />
      <span>{{ tax.toFormat() }}</span>
    </v-row>

    <v-divider />

    <v-row class="pa-1" dense>
      <span>Total</span>
      <v-spacer />
      <span>{{ orderTotal.toFormat() }}</span>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  computed: {
    ...mapState("cart", [
      "isValidCheckout",
      "tax_percentage",
      "order_id",
      "cart_products",
      "customer",
      "order_discount",
      "delivery_fees_price",
      "order_status"
    ]),

    deliveryFeesPrice() {
      return this.parsePrice(this.delivery_fees_price);
    },
    subTotalwDiscount() {
      let subtotal = this.parsePrice();

      this.cart_products.forEach(product => {
        const productPrice = this.$price(product.price).multiply(
          Number(product.qty)
        );
        const result = this.calcDiscount(productPrice, product.discount);
        subtotal = subtotal.add(productPrice).subtract(result);
      });

      const cartDiscount = this.calcDiscount(subtotal, this.order_discount);
      subtotal = subtotal.subtract(cartDiscount);
      return this.isValidCheckout ? subtotal : this.parsePrice();
    },
    tax() {
      if (this.customer && this.customer.no_tax) {
        return this.parsePrice();
      } else {
        const tax = this.subTotalwDiscount
          .add(this.deliveryFeesPrice)
          .percentage(this.tax_percentage);
        return this.isValidCheckout ? tax : this.parsePrice();
      }
    },
    totalDiscount() {
      let subtotalNoDiscount = this.parsePrice();

      this.cart_products.forEach(product => {
        const result = this.parsePrice(product.price).multiply(
          Number(product.qty)
        );
        subtotalNoDiscount = subtotalNoDiscount.add(result);
      });

      subtotalNoDiscount = subtotalNoDiscount.subtract(this.subTotalwDiscount);
      return this.isValidCheckout ? subtotalNoDiscount : this.parsePrice();
    },
    orderTotal() {
      const orderTotal = this.subTotalwDiscount
        .add(this.tax)
        .add(this.deliveryFeesPrice);
      return this.isValidCheckout ? orderTotal : this.parsePrice();
    }
  },

  watch: {
    orderTotal: {
      immediate: true,

      handler(value) {
        this.setOrderTotalPrice(value.toJSON());
        this.setOrderRemainingPrice(value.toJSON());
      }
    }
  },

  methods: {
    ...mapMutations("cart", [
      "setOrderTotalPrice",
      "setOrderRemainingPrice",
      "isValidCart"
    ]),

    calcDiscount(price, discount) {
      if (
        _.has(discount, "type") &&
        _.has(discount, "amount") &&
        discount.amount > 0
      ) {
        switch (discount.type) {
          case "flat":
            return this.$price({ amount: discount.amount });
          case "percentage":
            if (Number(discount.amount) > 100) {
              return this.parsePrice();
            } else {
              return price.percentage(Number(discount.amount));
            }
          default:
            return this.parsePrice();
        }
      } else {
        return this.parsePrice();
      }
    }
  }
};
</script>
