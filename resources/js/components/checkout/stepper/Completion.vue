<template>
  <v-container>
    <v-row justify="space-around" align="center">
      <h3 v-if="!changePrice.isZero() && changePrice.isPositive()">
        Change:
        <span class="amber--text" v-text="changePrice.toFormat()" />
      </h3>

      <orderReceipt />
    </v-row>
  </v-container>
</template>

<script>
import { mapState } from "vuex";
export default {
  computed: {
    ...mapState("cart", ["order_change_price"]),

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
