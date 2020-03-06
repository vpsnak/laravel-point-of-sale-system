<template>
  <div>
    <paymentHistory />

    <paymentActions v-show="showPaymentActions" />
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
    ...mapState("cart", ["order_id", "order_status"]),

    showPaymentActions() {
      switch (this.order_status) {
        case null:
        case "submitted":
        case "pending":
        case "pending_payment":
          return true;
        default:
          return false;
      }
    }
  },

  methods: {
    ...mapMutations("cart", [
      "setPayments",
      "setOrderChange",
      "setOrderRemainingPrice",
      "setOrderStatus"
    ])
  }
};
</script>
