<template>
  <v-container>
    <v-row justify="space-around" align="center" class="flex-column">
      <h3 v-if="changePrice.greaterThan(this.parsePrice())">
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
        return this.parsePrice();
      }
    }
  }
};
</script>
