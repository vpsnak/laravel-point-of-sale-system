<template>
  <div>
    <transactionHistory />

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
      if (_.has(this.order_status, "value")) {
        switch (this.order_status.value) {
          case undefined:
          case null:
          case "submitted":
          case "pending_payment":
            return true;
          default:
            return false;
        }
      } else {
        return true;
      }
    }
  }
};
</script>
