<template>
  <div>
    <paymentHistory />

    <paymentActions
      v-show="showPaymentActions"
      :loading="paymentActionsLoading"
      @sendPayment="sendPayment"
    />
  </div>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
  data() {
    return {
      paymentHistoryLoading: false,
      paymentActionsLoading: false,

      payment: {
        type: null,
        amount: null
      }
    };
  },

  computed: {
    ...mapState("cart", ["order_id", "order_status"]),

    showPaymentActions() {
      if (!this.order_status) {
        return true;
      } else {
        switch (this.order_status) {
          default:
          case "paid":
            return false;
          case "created":
          case "pending":
          case "pending_payment":
            return true;
        }
      }
    }
  },

  methods: {
    ...mapMutations("cart", [
      "setPayments",
      "setOrderChange",
      "setOrderRemaining",
      "setOrderStatus"
    ]),
    ...mapMutations(["setNotification"]),
    ...mapActions("requests", ["request"]),

    sendPayment(event) {
      this.paymentActionsLoading = true;

      let payload = {
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
          this.setOrderRemaining(response.remaining);
          this.setOrderStatus(response.order_status);

          this.setPayments(response.payment);

          if (payload.data.payment_type === "house-account") {
            this.$store.state.cart.customer.house_account_limit -=
              payload.data.amount;
          }

          const notification = {
            msg: "Payment received",
            type: "success"
          };
          this.setNotification(notification);
        })
        .catch(error => {
          console.error(error);
          if (_.has(error.response.data, "payment")) {
            this.setPayments(error.response.data.payment);
          }
        })
        .finally(() => {
          this.paymentActionsLoading = false;
          this.$store.state.cart.paymentLoading = false;
        });
    }
  }
};
</script>
