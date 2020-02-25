<template>
  <div>
    <v-divider />

    <v-btn
      dark
      :color="checkoutColor"
      block
      class="my-2"
      @click.stop="setCheckoutDialog(true)"
      :disabled="disabled || !isValidCheckout"
      >Checkout
    </v-btn>

    <v-divider />

    <div class="d-flex align-center justify-center pa-2">
      <v-tooltip bottom color="green">
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            @click="setDialog(cartRestoreDialog)"
            class="flex-grow-1"
            tile
            color="green"
            v-on="on"
            :disabled="!cartsOnHoldSize"
          >
            <v-icon>mdi-recycle</v-icon>
            <v-badge
              :value="cartsOnHoldSize > 0 ? true : false"
              color="deep-orange"
              :content="cartsOnHoldSize"
            >
            </v-badge>
          </v-btn>
        </template>
        <span>
          <b>Restore cart</b>
        </span>
      </v-tooltip>

      <v-tooltip bottom color="orange">
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            :disabled="disabled"
            @click="holdCart"
            class="flex-grow-1"
            tile
            color="orange"
            v-on="on"
          >
            <v-icon>pause</v-icon>
          </v-btn>
        </template>
        <span>
          <b>Hold current cart</b>
        </span>
      </v-tooltip>

      <v-tooltip bottom color="red">
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            @click.stop="setDialog(emptyCartDialog)"
            :disabled="disabled"
            class="flex-grow-1"
            tile
            color="red"
            v-on="on"
          >
            <v-icon>mdi-cart-remove</v-icon>
          </v-btn>
        </template>
        <span>
          <b>Empty current cart</b>
        </span>
      </v-tooltip>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
  props: {
    disabled: Boolean
  },

  mounted() {
    this.getCartsOnHoldSize();
    EventBus.$on("cart-actions-empty-current-cart", event => {
      if (event.payload) {
        this.resetState(event.payload);
      }
    });
    EventBus.$on("cart-actions-restore-cart", event => {
      if (event.payload) {
        this.getCartsOnHoldSize(event.payload);
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("cart-actions-empty-current-cart");
    EventBus.$off("cart-actions-restore-cart");
  },

  data() {
    return {
      cartsOnHoldSize: 0,

      emptyCartDialog: {
        show: true,
        icon: "mdi-cart-remove",
        titleCloseBtn: true,
        action: "confirmation",
        title: "Empty cart",
        content: "<p>Are you sure you want to empty the cart?</p>",
        persistent: true,
        eventChannel: "cart-actions-empty-current-cart"
      },
      cartRestoreDialog: {
        show: true,
        icon: "mdi-recycle",
        titleCloseBtn: true,
        title: "Restore cart",
        component: "cartRestoreDialog",
        persistent: true,
        eventChannel: "cart-actions-restore-cart"
      }
    };
  },

  computed: {
    ...mapState("cart", [
      "isValidCheckout",
      "method",
      "customer",
      "discount_type",
      "discount_amount",
      "notes",
      "order_total"
    ]),

    checkoutColor() {
      switch (this.method) {
        case "retail":
          return "primary";
        case "pickup":
          return "warning";
        case "delivery":
          return "purple";
      }
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", [
      "resetState",
      "setCheckoutDialog",
      "cart_products"
    ]),
    ...mapActions(["getAll", "create"]),
    ...mapActions("cart", ["submitOrder"]),

    holdCart() {
      let payload = {
        model: "carts",
        data: {
          cash_register_id: this.$store.state.cashRegister.id,
          cart: {
            products: this.cart_products,
            customer_id: this.$store.state.cart.customer
              ? this.$store.state.cart.customer.id
              : null
          },
          discount_type: this.discount_type,
          discount_amount: this.discount_amount,
          shipping: { notes: this.notes },
          product_count: Object.keys(this.cart_products).length,
          total_price: this.order_total
        }
      };
      this.create(payload).then(() => {
        this.getCartsOnHoldSize();
        this.resetState();
        // @TODO move notification to backend
        this.$store.commit("setNotification", {
          msg: "Cart added on hold list",
          type: "info"
        });
      });
    },
    showRestoreOnHoldCartDialog() {
      this.cartRestoreDialog = true;
    },
    getCartsOnHoldSize() {
      let payload = {
        model: "carts"
      };
      this.getAll(payload).then(response => {
        this.cartsOnHoldSize = _.size(response);
      });
    }
  }
};
</script>
