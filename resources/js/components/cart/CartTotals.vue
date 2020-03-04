<template>
  <div class="d-flex flex-column">
    <div class="d-flex justify-space-between pa-2" v-if="totalDiscount">
      <span>Total discount</span>
      <span>{{ displayPrice(totalDiscount) }}</span>
    </div>

    <v-divider v-if="totalDiscount" />

    <div class="d-flex justify-space-between pa-2">
      <span>Sub total w/ discount</span>
      <span>{{ displayPrice(subTotalwDiscount) }}</span>
    </div>

    <v-divider />

    <div class="d-flex justify-space-between pa-2" v-if="deliveryFeesPrice">
      <span>Delivery Fees</span>
      <span>{{ displayPrice(deliveryFeesPrice) }}</span>
    </div>

    <v-divider v-if="deliveryFeesPrice" />

    <div class="d-flex justify-space-between pa-2 bb-1">
      <span>Tax</span>
      <span>{{ displayPrice(tax) }}</span>
    </div>

    <v-divider />

    <div class="d-flex justify-space-between pa-2">
      <span>Total</span>
      <span>{{ displayPrice(order_total) }}</span>
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
      if (this.delivery_fees) {
        return this.displayPriceNoSign(this.delivery_fees);
      } else {
        return this.newPrice();
      }
    },
    subTotalwDiscount() {
      let subtotal = this.newPrice();

      this.cart_products.forEach(product => {
        subtotal = this.addPrice(
          subtotal,
          this.calcDiscount(
            product.price,
            product.qty,
            product.discount || null
          )
        );
      });

      return this.subtractPrice(
        subtotal,
        this.calcDiscount(subtotal, this.discount)
      );
    },
    tax() {
      if (this.customer && this.customer.no_tax) {
        return this.newPrice();
      } else {
        return this.calcTax(
          this.addPrice(this.subTotalwDiscount, this.deliveryFeesPrice),
          this.tax_percentage
        );
      }
    },
    totalDiscount() {
      let subtotalNoDiscount = this.newPrice();

      this.cart_products.forEach(product => {
        subtotalNoDiscount = this.multiplyPrice(product.price, product.qty);
      });

      this.setOrderTotal(this.addPrice(this.subTotalwDiscount, this.tax));
      this.isValidDiscount();

      return this.subtractPrice(subtotalNoDiscount, this.subTotalwDiscount);
    }
  },
  methods: {
    ...mapMutations("cart", ["setOrderTotal", "isValidDiscount"])

    // calcDiscount(price, discount) {
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
