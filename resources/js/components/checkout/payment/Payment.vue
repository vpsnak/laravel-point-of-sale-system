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
    ]),
    ...mapMutations(["setNotification"]),
    ...mapActions("requests", ["request"]),

    sendPayment(event) {
      this.paymentActionsLoading = true;

      const payload = {
        method: "post",
        url: "payments/create",
        data: {
          payment_type: event.paymentType,
          amount: event.paymentAmount || null,
          order_id: this.order_id
        }
      };

      switch (event.paymentType) {
        default:
        case "cash":
          break;
        case "card":
          payload.data["card"] = event.card;
          break;
        case "coupon":
        case "giftcard":
          payload.data["code"] = event.code;
          break;
        case "house-account":
          payload.data["house_account_number"] = event.house_account_number;
          break;
      }

      this.request(payload)
        .then(response => {
          this.setOrderChange(response.change);
          this.setOrderRemainingPrice(response.remaining);
          this.setOrderStatus(response.order_status);

          this.setPayments(response.payment);

          if (payload.data.payment_type === "house-account") {
            this.$store.state.cart.customer.house_account_limit -=
              payload.data.amount;
          }
        })
        .catch(error => {
          console.error(error);
          if (_.has(error, "payment")) {
            this.setPayments(error.payment);
          }
        })
        .finally(() => {});
    }
  }
};
</script>
