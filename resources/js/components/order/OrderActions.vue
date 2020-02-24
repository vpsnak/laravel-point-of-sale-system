<template>
  <div class="text-center">
    <v-bottom-sheet v-model="orderPageActions">
      <v-list>
        <v-subheader>Actions</v-subheader>
        <v-list-item
          @click.stop="editOrderItems()"
          :disabled="loading"
          :loading="checkout_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-package-variant</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Edit order items</v-list-item-title>
        </v-list-item>
        <v-list-item
          v-if="canEditDelivery"
          @click.stop="editOrderDelivery()"
          :disabled="loading"
          :loading="checkout_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-package-variant</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Edit order delivery</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click.stop="saveChanges()"
          v-if="canSave"
          :disabled="loading"
          :loading="checkout_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-content-save-edit-outline</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Save changes</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click.stop="checkout()"
          v-if="canCheckout"
          :disabled="loading"
          :loading="checkout_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-currency-usd</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Continue checkout</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click.stop="receipt()"
          v-if="canReceipt"
          :disabled="loading"
          :loading="receipt_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-receipt</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Receipt</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click.stop="reorder()"
          :disabled="loading"
          :loading="reorder_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-cart-arrow-down</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Reorder</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click.stop="uploadToMas()"
          v-if="canUploadToMas"
          :disabled="loading"
          :loading="upload_to_mas_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-file-upload-outline</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Upload to MAS</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click.stop="refund()"
          v-if="canRefund"
          :disabled="loading"
          :loading="refund_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-credit-card-refund-outline</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Make a refund</v-list-item-title>
        </v-list-item>
        <v-list-item
          color="red"
          @click.stop="cancelDialog()"
          v-if="canCancel"
          :disabled="loading"
          :loading="cancel_loading"
        >
          <v-list-item-avatar>
            <v-icon color="red">mdi-cancel</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Cancel order</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-bottom-sheet>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
  mounted() {
    EventBus.$on("order-edit-cancel-order", event => {
      if (event.payload) {
        this.cancelOrder();
      } else {
        this.cancel_loading = false;
      }
    });

    EventBus.$on("order-edit-refund-confirmation", event => {
      this.refund_loading = false;
      if (event.payload) {
        this.setRefundDialog();
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
      "payments",
      "order_page_actions"
    ]),

    orderPageActions: {
      get() {
        return this.order_page_actions;
      },
      set(value) {
        this.setOrderPageActions(value);
      }
    },
    loading() {
      if (
        this.checkout_loading ||
        this.reorder_loading ||
        this.receipt_loading ||
        this.upload_to_mas_loading ||
        this.refund_loading ||
        this.cancel_loading
      ) {
        this.setOrderPageActions(false);
        return true;
      } else {
        return false;
      }
    },
    canEditDelivery() {
      if (this.method !== "retail") {
        return true;
      } else {
        return false;
      }
    },
    canSave() {
      if (true) {
        return true;
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
        this.order_status !==
          ["completed", "canceled"].indexOf(this.order_status) >= 0
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
    ...mapMutations("dialog", ["setDialog", "resetDialog"]),
    ...mapMutations("cart", [
      "setCheckoutDialog",
      "resetState",
      "setReorder",
      "setOrderPageActions"
    ]),
    ...mapActions("requests", ["request"]),

    editOrderItems() {
      this.setDialog({
        show: true,
        fullscreen: true,
        title: `Edit items for Order #${this.order_id}`,
        titleCloseBtn: true,
        icon: "mdi-package-variant",
        component: "sales",
        component_props: { order_edit: 1 },
        persistent: true,
        eventChannel: "order-page-edit-items"
      });
    },
    refund() {
      // this.refund_loading = true;
      // const payload = {
      //   title: "Verify your password to issue a refund",
      //   eventChannel: "order-edit-refund-confirmation"
      // };
      // this.confirmationDialog(payload);

      this.setDialog({
        show: true,
        width: 700,
        title: `Issue a refund for order #${this.order_id}`,
        titleCloseBtn: true,
        icon: "mdi-credit-card-refund-outline",
        component: "orderRefundForm",
        persistent: true,
        eventChannel: "order-edit-refund"
      });
    },
    setRefundDialog() {
      this.setDialog({
        show: true,
        width: 700,
        title: `Issue a refund for order #${this.order_id}`,
        titleCloseBtn: true,
        icon: "mdi-credit-card-refund-outline",
        component: "orderRefundForm",
        persistent: true,
        eventChannel: "order-edit-refund"
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
        title: `Receipt #${this.order_id}`,
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
      const payload = {
        title: `Verify your password to cancel order #${this.order_id}`,
        eventChannel: "order-edit-cancel-order"
      };

      this.confirmationDialog(payload);
    },
    confirmationDialog(payload) {
      this.setDialog({
        show: true,
        width: 600,
        title: payload.title,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: payload.eventChannel
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
