<template>
  <div class="text-center">
    <v-bottom-sheet v-model="orderPageActions">
      <v-list>
        <v-subheader>Edit</v-subheader>
        <v-list-item
          @click.stop="editOrderItems()"
          :disabled="loading"
          :loading="checkout_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-package-variant</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Order items</v-list-item-title>
        </v-list-item>
        <v-list-item
          v-if="canEditOrderOptions"
          @click.stop="editOrderOptions()"
          :disabled="loading"
          :loading="checkout_loading"
        >
          <v-list-item-avatar>
            <v-icon>mdi-file-document-edit-outline</v-icon>
          </v-list-item-avatar>
          <v-list-item-title>Order options</v-list-item-title>
        </v-list-item>
        <v-divider />
        <v-subheader>Actions</v-subheader>
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
import { EventBus } from "../../plugins/eventBus";

export default {
  mounted() {
    EventBus.$on("order-edit-cancel-order", event => {
      if (event.payload) {
        this.cancelOrder();
      } else {
        this.cancel_loading = false;
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("order-edit-cancel-order");
  },

  data() {
    return {
      checkout_loading: false,
      reorder_loading: false,
      receipt_loading: false,
      upload_to_mas_loading: false,
      cancel_loading: false
    };
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "cart_products",
      "order_status",
      "transactions",
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
        this.cancel_loading
      ) {
        this.setOrderPageActions(false);
        return true;
      } else {
        return false;
      }
    },
    canEditOrderOptions() {
      if (this.method !== "retail") {
        return true;
      } else {
        return false;
      }
    },
    canCheckout() {
      if (
        (this.order_status ===
          ["pending", "payment_pending"].indexOf(this.order_status)) !==
        -1
      ) {
        return true;
      } else {
        return false;
      }
    },
    canReceipt() {
      return this.order_status.can_receipt;
    },
    canUploadToMas() {
      return this.order_status.can_upload_to_mas;
    },
    canCancel() {
      if (
        (this.order_status !==
          ["canceled", "completed"].indexOf(this.order_status)) !==
        -1
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
      this.$router.push({ name: "editOrderItemsPage" });
    },
    editOrderOptions() {
      this.$router.push({ name: "editOrderOptionsPage" });
    },
    reorder() {
      this.reorder_loading = true;
      const items = this.cart_products;
      this.resetState();
      this.setReorder(items);
      this.$router.replace({ name: "sale" });
      this.setCheckoutDialog(true);
      this.reorder_loading = false;
    },
    checkout() {
      this.checkout_loading = true;
      this.$router.replace({ name: "sale" });
      this.setCheckoutDialog(true);
      this.checkout_loading = false;
    },
    receipt() {
      const payload = {
        show: true,
        width: 600,
        title: `Receipt #${this.order_id}`,
        titleCloseBtn: true,
        icon: "mdi-receipt",
        component: "orderReceipt",
        persistent: true,
        eventChannel: "orders-table-receipt"
      };
      this.setDialog(payload);
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
