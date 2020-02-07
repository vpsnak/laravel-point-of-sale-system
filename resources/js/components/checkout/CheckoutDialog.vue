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
        <v-tooltip bottom color="red">
          <template v-slot:activator="{ on }">
            <v-btn
              :disabled="disableControls"
              @click.stop="close"
              icon
              v-on="on"
              color="red"
            >
              <v-icon>
                {{ order.id ? "mdi-cancel" : "mdi-close" }}
              </v-icon>
            </v-btn>
          </template>
          <span>{{ closeBtnTxt }}</span>
        </v-tooltip>

        <v-tooltip bottom v-if="order.id" color="orange">
          <template v-slot:activator="{ on }">
            <v-btn
              :disabled="disableControls"
              @click="hold"
              icon
              v-on="on"
              color="orange"
            >
              <v-icon>mdi-pause</v-icon>
            </v-btn>
          </template>
          <span>Hold</span>
        </v-tooltip>

        <v-toolbar-title>Checkout</v-toolbar-title>
      </v-toolbar>

      <v-card-text>
        <v-row dense>
          <v-col :lg="8">
            <checkoutStepper />
          </v-col>
          <v-col :lg="4">
            <cart
              :key="order.id"
              icon="mdi-clipboard-list"
              title="Order summary"
              :editable="order.id ? false : true"
            ></cart>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapMutations, mapActions, mapState } from "vuex";

export default {
  beforeDestroy() {
    this.$off("close");
  },
  data() {
    return {
      confirmationDialog: {
        show: "closePrompt",
        action: "confirmation",
        title: "Cancel order?",
        content: "Are you sure you want to <b>cancel</b> the current order?",
        actions: true,
        persistent: true
      }
    };
  },
  watch: {
    orderLoading(value) {
      console.log("order-loading: " + value);
    },
    loading(value) {
      console.log("loading: " + value);
    }
  },
  computed: {
    ...mapState("cart", [
      "order",
      "checkoutDialog",
      "currentCheckoutStep",
      "paymentLoading",
      "refundLoading",
      "completeOrderLoading"
    ]),

    cart() {
      return this.$store.state.cart;
    },
    disableControls() {
      if (
        this.paymentLoading ||
        this.refundLoading ||
        this.completeOrderLoading
      ) {
        return true;
      } else {
        return false;
      }
    },
    closeBtnTxt() {
      return this.order.id && this.currentCheckoutStep !== 3
        ? "Cancel order"
        : "Close";
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
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", ["resetState"]),
    ...mapMutations("cart", ["setCheckoutDialog"]),
    ...mapActions(["delete"]),

    hold() {
      this.resetState();
    },
    close() {
      if (this.order.id && this.order.status === "complete") {
        this.resetState();
      } else if (this.order.id && this.order.status !== "complete") {
        this.setDialog(this.confirmationDialog);
      } else {
        this.state = false;
      }
    },
    confirmation(event) {
      if (event) {
        this.$store.state.cart.isValidCheckout = false;

        let payload = {
          model: "orders",
          id: this.order.id
        };
        this.delete(payload).then(response => {
          this.resetState();
        });
      }
    }
  }
};
</script>
