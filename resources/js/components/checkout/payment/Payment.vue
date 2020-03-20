<template>
  <div>
    <transactionHistory checkout />

    <paymentActions v-if="showPaymentActions" />
  </div>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
  data() {
    return {
      payment: {
        type: null,
        amount: null
      }
    };
  },

  computed: {
    ...mapState("cart", ["order_status"]),

    showPaymentActions() {
      // @TODO use order status can_checkout
      if (_.has(this.order_status, "can_checkout")) {
        return this.order_status.can_checkout;
      } else {
        return true;
      }
    }
  }
};
</script>
