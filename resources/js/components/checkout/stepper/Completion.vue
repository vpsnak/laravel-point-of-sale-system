<template>
  <div
    class="my-5 d-flex align-center justify-space-around flex-column"
    v-if="order_status === 'paid'"
  >
    <h3 v-if="!changePrice.isZero() && changePrice.isPositive()">
      Change:
      <span class="amber--text" v-text="changePrice.toFormat()" />
    </h3>

    <orderReceipt />
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  computed: {
    ...mapState("cart", ["order_status", "order_change_price"]),

    changePrice() {
      if (this.order_change_price) {
        return this.parsePrice(this.order_change_price);
      } else {
        return this.$price();
      }
    }
  }
};
</script>
