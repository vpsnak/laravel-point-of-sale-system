<template>
  <div>
    <transactionHistory checkout />

    <paymentActions v-if="showPaymentActions" />
  </div>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
  computed: {
    ...mapState("cart", ["order_status"]),

    showPaymentActions() {
      if (_.has(this.order_status, "can_checkout")) {
        return this.order_status.can_checkout;
      } else {
        return true;
      }
    },
  },
};
</script>
