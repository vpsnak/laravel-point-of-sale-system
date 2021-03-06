<template>
  <v-container>
    <v-row
      v-if="!totalDiscount.isZero()"
      justify="space-between"
      align="center"
      dense
    >
      <v-col cols="auto">
        <span v-text="'Total discount'" />
      </v-col>
      <v-col cols="auto">
        <span
          :class="isValidCheckout ? '' : 'error--text'"
          v-html="
            isValidCheckout
              ? displayPrice(totalDiscount)
              : '<b>Discount error</b>'
          "
        />
      </v-col>
    </v-row>

    <v-divider v-if="!totalDiscount.isZero()" />

    <v-row justify="space-between" align="center" dense>
      <v-col cols="auto">
        <span v-text="'Sub total w/ discount'" />
      </v-col>
      <v-col cols="auto">
        <span v-text="displayPrice(subTotalwDiscount)" />
      </v-col>
    </v-row>

    <v-divider />

    <v-row
      v-if="!deliveryFeesPrice.isZero()"
      justify="space-between"
      align="center"
      dense
    >
      <v-col cols="auto">
        <span v-text="'Delivery Fees'" />
      </v-col>
      <v-col cols="auto">
        <span v-text="displayPrice(deliveryFeesPrice)" />
      </v-col>
    </v-row>

    <v-divider v-if="!deliveryFeesPrice.isZero()" />

    <v-row justify="space-between" align="center" dense>
      <v-col cols="auto">
        <span v-text="'Tax'" />
      </v-col>
      <v-col cols="auto">
        <span v-text="displayPrice(tax)" />
      </v-col>
    </v-row>

    <v-divider />

    <v-row justify="space-between" align="center" dense>
      <v-col cols="auto">
        <span v-text="'Total'" />
      </v-col>
      <v-col cols="auto">
        <span v-text="displayPrice(orderTotal)" />
      </v-col>
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
      "order_status",
    ]),

    deliveryFeesPrice() {
      return this.parsePrice(this.delivery_fees_price);
    },
    subTotalwDiscount() {
      let subtotal = this.parsePrice();

      this.cart_products.forEach((product) => {
        const productPrice = this.parsePrice(product.price).multiply(
          Number(product.qty)
        );
        const result = this.calcDiscount(productPrice, product.discount);
        subtotal = subtotal.add(productPrice).subtract(result);
      });

      const cartDiscount = this.calcDiscount(subtotal, this.order_discount);
      subtotal = subtotal.subtract(cartDiscount);
      return subtotal;
    },
    tax() {
      if (this.customer && this.customer.no_tax) {
        return this.parsePrice();
      } else {
        const tax = this.subTotalwDiscount
          .add(this.deliveryFeesPrice)
          .percentage(this.tax_percentage);
        return tax;
      }
    },
    totalDiscount() {
      let subtotalNoDiscount = this.parsePrice();

      this.cart_products.forEach((product) => {
        const result = this.parsePrice(product.price).multiply(
          Number(product.qty)
        );
        subtotalNoDiscount = subtotalNoDiscount.add(result);
      });

      subtotalNoDiscount = subtotalNoDiscount.subtract(this.subTotalwDiscount);
      return subtotalNoDiscount;
    },
    orderTotal() {
      const orderTotal = this.subTotalwDiscount
        .add(this.tax)
        .add(this.deliveryFeesPrice);
      return orderTotal;
    },
  },

  watch: {
    orderTotal: {
      immediate: true,

      handler(value) {
        this.setOrderTotalPrice(value.toJSON());
        this.setOrderRemainingPrice(value.toJSON());
      },
    },
  },

  methods: {
    ...mapMutations("cart", ["setOrderTotalPrice", "setOrderRemainingPrice"]),

    displayPrice(price) {
      if (price.isPositive() || price.isZero()) {
        return price.toFormat();
      } else {
        return this.parsePrice().toFormat();
      }
    },
    calcDiscount(price, discount) {
      if (
        _.has(discount, "type") &&
        _.has(discount, "amount") &&
        discount.amount > 0
      ) {
        switch (discount.type) {
          case "flat":
            return this.parsePrice({ amount: discount.amount });
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
    },
  },
};
</script>
