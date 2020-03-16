<template>
  <v-dialog
    v-model="state"
    fullscreen
    transition="dialog-bottom-transition"
    persistent
    no-click-animation
  >
    <v-card>
      <v-toolbar>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              :disabled="disableControls"
              @click.stop="close()"
              icon
              v-on="on"
              color="red"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
          <span>Close</span>
        </v-tooltip>

        <v-toolbar-title>Checkout</v-toolbar-title>
      </v-toolbar>

      <v-card-text>
        <v-row dense>
          <v-col :lg="8">
            <checkoutStepper v-if="checkoutDialog" />
          </v-col>
          <v-col :lg="4">
            <cart
              :editable="order_id ? false : true"
              :showMethods="true"
              :showCustomer="true"
            ></cart>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapMutations, mapActions, mapState } from "vuex";
import { EventBus } from "../../plugins/eventBus";

export default {
  mounted() {
    EventBus.$on("checkout-cancel-order", event => {
      if (event.payload) {
        this.setIsValidCheckout(false);
        this.request({
          method: "delete",
          url: `orders/${this.order_id}`
        }).finally(() => {
          this.resetState();
        });
      }
    });
  },

  beforeDestroy() {
    this.$off("close");
    EventBus.$off("checkout-cancel-order");
  },

  computed: {
    ...mapState("cart", [
      "checkout_loading",
      "cart_products",
      "order_id",
      "order_status",
      "checkoutDialog",
      "currentCheckoutStep",
      "completeOrderLoading"
    ]),

    disableControls() {
      if (this.checkout_loading) {
        return true;
      } else {
        return false;
      }
    },
    state: {
      get() {
        return this.checkoutDialog;
      },
      set(value) {
        this.setCheckoutDialog(value);
      }
    }
  },

  watch: {
    state(v) {
      if (!v) {
        this.$emit("close", true);
      }
    },
    cart_products(v) {
      if (v.length === 0) {
        this.state = false;
      }
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", [
      "setCheckoutDialog",
      "setIsValidCheckout",
      "resetState"
    ]),
    ...mapActions("requests", ["request"]),

    close() {
      if (this.order_status === "complete" || this.order_id) {
        this.resetState();
      } else {
        this.state = false;
      }
    }
  }
};
</script>
