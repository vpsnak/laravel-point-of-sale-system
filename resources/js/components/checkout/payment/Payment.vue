<template>
  <div>
    <paymentHistory @refund="refund"></paymentHistory>

    <paymentActions
      v-show="showPaymentActions"
      :loading="paymentActionsLoading"
      @sendPayment="sendPayment"
    ></paymentActions>
  </div>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
  data() {
    return {
      paymentTypes: [],

      paymentHistoryLoading: false,
      paymentActionsLoading: false,

      payment: {
        type: null,
        amount: null
      }
    };
  },

  mounted() {
    // if (this.order_id) {
    //     this.remaining = (this.order.total - this.order.total_paid).toFixed(
    //         2
    //     );
    //     let payload = {};
    //     if (this.remaining < 0) {
    //         payload = {
    //             order_status: this.order.status,
    //             change: Math.abs(this.remaining)
    //         };
    //     } else {
    //         payload = {
    //             order_status: this.order.status,
    //             change: false
    //         };
    //     }
    //     this.$emit("payment", payload);
    // }
  },

  computed: {
    ...mapState("cart", ["order_id", "order_status", "payments"]),

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
    },

    paymentType: {
      get() {
        return this.payment.type;
      },
      set(value) {
        this.payment.type = value;
      }
    },
    paymentAmount: {
      get() {
        return this.payment.amount;
      },
      set(value) {
        this.payment.amount = value;
      }
    },
    refundLoading: {
      get() {
        return this.$store.state.cart.refundLoading;
      },
      set(value) {
        this.$store.state.cart.refundLoading = value;
      }
    }
  },

  methods: {
    ...mapMutations("cart", [
      "setPaymentHistory",
      "setOrderChange",
      "setOrderRemaining",
      "setOrderStatus",
      "setRefundLoading",
      "setPaymentRefundedStatus"
    ]),
    ...mapMutations(["setNotification"]),
    ...mapActions(["search", "create", "getAll", "getOne"]),

    sendPayment(event) {
      this.paymentActionsLoading = true;

      let payload = {
        model: "payments",
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

      this.create(payload)
        .then(response => {
          console.log(response);
          this.setOrderChange(response.change);
          this.setOrderRemaining(response.remaining);
          this.setOrderStatus(response.order_status);

          this.setPaymentHistory(response.payment);

          if (payload.data.payment_type === "house-account") {
            this.$store.state.cart.customer.house_account_limit -=
              payload.data.amount;
          }

          let notification = {
            msg: "Payment received",
            type: "success"
          };
          this.setNotification(notification);
        })
        .catch(error => {
          console.error(error);
          if (_.has(error.response.data, "payment")) {
            this.setPaymentHistory(error.response.data.payment);
          }
        })
        .finally(() => {
          this.paymentAmount = null;
          this.paymentActionsLoading = false;
          this.$store.state.cart.paymentLoading = false;
        });
    },
    refund(event) {
      if (event.refunded_payment_id) {
        const index = _.findIndex(this.payments, [
          "id",
          event.refunded_payment_id
        ]);

        this.setPaymentRefundedStatus(index);
        this.setPaymentHistory(event.refund);
      }

      this.setOrderChange(event.change);
      this.setOrderRemaining(event.remaining);
      this.setOrderStatus(event.order_status);

      const notification = {
        msg: event.msg,
        type: event.status
      };

      this.setRefundLoading(false);
      this.setNotification(notification);
    }
  }
};
</script>
