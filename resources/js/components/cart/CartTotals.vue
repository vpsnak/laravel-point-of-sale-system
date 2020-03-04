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
      <span>{{ $price(order_total).toFormat("$0,0.00") }}</span>
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
      if (this.delivery_fees_price) {
        return this.delivery_fees_price.toFormat("0,0.00");
      } else {
        return this.$price();
      }
    },
    subTotalwDiscount() {
      var subtotal = this.$price();

      this.cart_products.forEach(product => {
        const productPrice = this.$price(product.price).multiply(
          Number(product.qty)
        );
        // const result = this.calcDiscount(productPrice, product.discount);
        const result = productPrice;
        subtotal = this.$price(subtotal).add(result);
        console.log(productPrice.toFormat("$0,0.00"));
        console.log(result.toFormat("$0,0.00"));

        console.log(subtotal.toFormat("$0,0.00"));
      });
      // const cartDiscount = this.calcDiscount(subtotal, this.discount);

      return this.$price(subtotal).subtract(this.$price());
      // return this.$price(subtotal).subtract(cartDiscount);
    },
    tax() {
      if (this.customer && this.customer.no_tax) {
        return this.$price();
      } else {
        return (
          this.$price(this.subTotalwDiscount)
            // .add(this.delivery_fees_price)
            .percentage(this.tax_percentage)
        );
      }
    },
    totalDiscount() {
      var subtotalNoDiscount = this.$price();

      this.cart_products.forEach(product => {
        const result = this.$price(product.price).multiply(Number(product.qty));
        subtotalNoDiscount = this.$price(subtotalNoDiscount).add(result);
      });
      this.isValidDiscount();

      return this.$price(subtotalNoDiscount).subtract(this.subTotalwDiscount);
    }
  },
  methods: {
    ...mapMutations("cart", ["setOrderTotal", "isValidDiscount"])

    // calcDiscountOld(price, discount) {
    //   if (discount && _.has(discount, "price") && _.has(discount, "type")) {
    //     switch (_.lowerCase(type)) {
    //       case "flat":
    //         return parseFloat(price).toFixed(2) - parseFloat(amount).toFixed(2);
    //       case "percentage":
    //         return (
    //           parseFloat(price) - (parseFloat(price) * parseFloat(amount)) / 100
    //         );
    //       default:
    //         return parseFloat(price);
    //     }
    //   }
    //   return parseFloat(price);
    // }
  }
};
</script>
