<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>mdi-cursor-default-click-outline</v-icon>
      <span class="subheading">
        Actions
      </span>
    </v-card-title>
    <v-container fluid>
      <v-row justify="space-between" align="center" no-gutters>
        <v-btn
          v-if="canCheckout"
          @click.stop="checkout()"
          :disabled="loading"
          :loading="checkout_loading"
        >
          <v-icon left>mdi-currency-usd</v-icon>
          Continue checkout
        </v-btn>

        <v-btn
          v-if="canReceipt"
          @click.stop="receipt()"
          :disabled="loading"
          :loading="receipt_loading"
        >
          <v-icon left>
            mdi-receipt
          </v-icon>
          Receipt
        </v-btn>

        <v-btn
          @click.stop="reorder()"
          :disabled="loading"
          :loading="reorder_loading"
        >
          <v-icon left>
            mdi-cart-arrow-down
          </v-icon>
          Reorder
        </v-btn>

        <v-btn
          v-if="canUploadToMas"
          @click.stop="uploadToMas()"
          :disabled="loading"
          :loading="upload_to_mas_loading"
        >
          <v-icon left>
            mdi-file-upload-outline
          </v-icon>
          Upload to MAS
        </v-btn>

        <v-btn
          v-if="canRefund"
          @click.stop="refund()"
          :disabled="loading"
          :loading="refund_loading"
        >
          <v-icon left>
            mdi-credit-card-refund-outline
          </v-icon>
          Make a refund
        </v-btn>

        <v-btn
          v-if="canCancel"
          @click.stop="cancelDialog()"
          :disabled="loading"
          :loading="cancel_loading"
        >
          <v-icon left color="red">
            mdi-cancel
          </v-icon>
          Cancel order
        </v-btn>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
  mounted() {
    EventBus.$on("order-edit-cancel-order", event => {
      console.log(event);
      if (event.payload) {
        this.cancelOrder();
      } else {
        this.cancel_loading = false;
      }
    });
  },

  beforeDestroy() {
    EventBus.$off();
  },

  data() {
    return {
      checkout_loading: false,
      reorder_loading: false,
      receipt_loading: false,
      upload_to_mas_loading: false,
      refund_loading: false,
      cancel_loading: false
    };
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "cart_products",
      "order_status",
      "payments"
    ]),

    loading() {
      if (
        this.checkout_loading ||
        this.reorder_loading ||
        this.receipt_loading ||
        this.upload_to_mas_loading ||
        this.refund_loading ||
        this.cancel_loading
      ) {
        return true;
      } else {
        return false;
      }
    },
    canCheckout() {
      if (
        this.order_status ===
        ["pending", "pending_payment"].indexOf(this.order_status) >= 0
      ) {
        return true;
      } else {
        return false;
      }
    },
    canReceipt() {
      if (this.order_status === "paid") {
        return true;
      } else {
        return false;
      }
    },
    canUploadToMas() {
      if (true) {
        return true;
      } else {
        return false;
      }
    },

    canRefund() {
      if (
        this.payments.length > 0 &&
        this.order_status ===
          ["pending", "pending_payment"].indexOf(this.order_status) >= 0
      ) {
        return true;
      } else {
        return false;
      }
    },

    canCancel() {
      if (
        this.order_status !==
        ["canceled", "completed"].indexOf(this.order_status) >= 0
      ) {
        return true;
      } else {
        return false;
      }
    }
  },
  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", ["setCheckoutDialog", "resetState", "setReorder"]),
    ...mapActions("requests", ["request"]),

    refund() {
      this.setDialog({
        show: true,
        width: 600,
        title: `Refunds for order #${order_id}`,
        titleCloseBtn: true,
        icon: "mdi-alpha-z-circle",
        component: "closeCashRegisterForm",
        persistent: true,
        eventChannel: "top-menu-generate-z"
      });
    },
    reorder() {
      this.reorder_loading = true;
      const items = this.cart_products;
      this.resetState();
      this.setReorder(items);
      this.$router.replace({ name: "sales" });
      this.setCheckoutDialog(true);
      this.reorder_loading = false;
    },
    checkout() {
      this.checkout_loading = true;
      this.$router.replace({ name: "sales" });
      this.setCheckoutDialog(true);
      this.checkout_loading = false;
    },
    receipt() {
      this.setDialog({
        show: true,
        width: 600,
        title: `Receipt #${id}`,
        titleCloseBtn: true,
        icon: "mdi-receipt",
        component: "orderReceipt",
        persistent: true,
        eventChannel: "orders-table-receipt"
      });
    },
    uploadToMas() {},
    cancelDialog() {
      this.cancel_loading = true;

      this.setDialog({
        show: true,
        width: 600,
        title: `Verify your password to cancel order #${this.order_id}`,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: "order-edit-cancel-order"
      });
    },
    cancelOrder() {
      return new Promise((resolve, reject) => {
        const payload = {
          method: "delete",
          url: `orders/${this.order_id}`
        };

        this.request(payload)
          .then(() => {
            this.$router.replace({ name: "orders" });
            this.resetState();
          })
          .finally(() => {
            this.cancel_loading = false;
          });
      });
    }
  }
};
</script>
