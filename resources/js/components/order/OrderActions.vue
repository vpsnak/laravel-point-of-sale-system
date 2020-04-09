<template>
  <v-bottom-sheet v-model="orderPageActions">
    <v-list>
      <v-subheader v-if="canEditOrderItems || canEditOrderOptions">
        <h3 v-text="'Edit'" />
      </v-subheader>
      <v-list-item
        v-if="canEditOrderItems"
        @click.stop="editOrderItems()"
        :disabled="loading"
        :loading="checkout_loading"
      >
        <v-list-item-icon>
          <v-icon v-text="'mdi-package-variant'" />
        </v-list-item-icon>
        <v-list-item-title>
          <span v-text="'Order items'" />
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        v-if="canEditOrderOptions"
        @click.stop="editOrderOptions()"
        :disabled="loading"
        :loading="checkout_loading"
      >
        <v-list-item-icon>
          <v-icon v-text="'mdi-file-document-edit-outline'" />
        </v-list-item-icon>
        <v-list-item-title>
          <span v-text="'Order options'" />
        </v-list-item-title>
      </v-list-item>
      <v-divider />
      <v-list-item
        v-if="canCheckout"
        @click.stop="checkout()"
        :disabled="loading"
        :loading="checkout_loading"
      >
        <v-list-item-icon>
          <v-icon v-text="'mdi-currency-usd'" />
        </v-list-item-icon>
        <v-list-item-title>
          <span v-text="'Continue checkout'" />
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        v-if="canReceipt"
        @click.stop="receipt()"
        :disabled="loading"
        :loading="receipt_loading"
      >
        <v-list-item-icon>
          <v-icon v-text="'mdi-receipt'" />
        </v-list-item-icon>
        <v-list-item-title>
          <span v-text="'Receipt'" />
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        @click.stop="reorder()"
        :disabled="loading"
        :loading="reorder_loading"
      >
        <v-list-item-icon>
          <v-icon v-text="'mdi-cart-arrow-down'" />
        </v-list-item-icon>
        <v-list-item-title>
          <span v-text="'Reorder'" />
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        v-if="canUploadToMas"
        @click.stop="uploadToMas()"
        :disabled="loading"
        :loading="upload_to_mas_loading"
      >
        <v-list-item-icon>
          <v-icon v-text="'>mdi-file-upload-outline'" />
        </v-list-item-icon>
        <v-list-item-title>
          <span v-text="'Upload to MAS'" />
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        @click.stop="cancelDialog()"
        v-if="canCancel"
        :disabled="loading"
        :loading="cancel_loading"
      >
        <v-list-item-icon>
          <v-icon v-text="'mdi-cancel'" />
        </v-list-item-icon>
        <v-list-item-title>
          <span v-text="'Cancel order'" />
        </v-list-item-title>
      </v-list-item>
    </v-list>
  </v-bottom-sheet>
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
      cancel_loading: false,
      confirmationDialog: {
        show: true,
        width: 600,
        title: null,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        component_props: { action: "verify" },
        persistent: true,
        eventChannel: null
      }
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
    canEditOrderItems() {
      return this.order_status.can_edit_order_items;
    },
    canEditOrderOptions() {
      return this.order_status.can_edit_order_options;
    },
    canCheckout() {
      return this.order_status.can_checkout;
    },
    canReceipt() {
      return this.order_status.can_receipt;
    },
    canUploadToMas() {
      return this.order_status.can_upload_to_mas;
    },
    canCancel() {
      return this.order_status.can_cancel;
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
      this.setDialog({ ...this.confirmationDialog, ...payload });
    },

    cancelOrder() {
      return new Promise((resolve, reject) => {
        const payload = {
          method: "get",
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
