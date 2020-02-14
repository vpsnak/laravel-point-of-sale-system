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
                {{ order_id ? "mdi-cancel" : "mdi-close" }}
              </v-icon>
            </v-btn>
          </template>
          <span>{{ closeBtnTxt }}</span>
        </v-tooltip>

        <v-tooltip bottom v-if="order_id" color="orange">
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
            <checkoutStepper v-if="checkoutDialog" />
          </v-col>
          <v-col :lg="4">
            <cart
              icon="mdi-clipboard-list"
              title="Order summary"
              :editable="order_id ? false : true"
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

  computed: {
    ...mapState("cart", [
      "order_id",
      "order_status",
      "checkoutDialog",
      "currentCheckoutStep",
      "paymentLoading",
      "refundLoading",
      "completeOrderLoading"
    ]),
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
      return this.order_id && this.currentCheckoutStep !== 3
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
    ...mapMutations("cart", [
      "setCheckoutDialog",
      "setIsValidCheckout",
      "resetState"
    ]),
    ...mapActions(["delete"]),

    hold() {
      this.resetState();
    },
    close() {
      if (this.order_status === "complete") {
        this.resetState();
      } else if (this.order_id && this.order_status !== "complete") {
        this.setDialog({
          show: true,
          action: "confirmation",
          title: "Cancel order?",
          content: "Are you sure you want to <b>cancel</b> the current order?",
          actions: true,
          persistent: true,
          cancelBtnTxt: "No",
          confirmationBtnTxt: "Yes"
        });
      } else {
        this.state = false;
      }
    },
    confirmation(event) {
      if (event) {
        this.setIsValidCheckout(false);

        let payload = {
          model: "orders",
          id: this.order_id
        };
        this.delete(payload).then(response => {
          this.resetState();
        });
      }
    }
  }
};
</script>
