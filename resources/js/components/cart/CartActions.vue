<template>
  <div>
    <v-divider />

    <v-btn
      dark
      :color="checkoutColor"
      block
      class="my-2"
      @click.stop="setCheckoutDialog(true)"
      :disabled="disabled || !$store.state.cart.isValidCheckout"
      >Checkout</v-btn
    >

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
import { mapActions, mapMutations } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
  data() {
    return {
      cartsOnHoldSize: 0,

      emptyCartDialog: {
        icon: "mdi-cart-remove",
        titleCloseBtn: true,
        action: "confirmation",
        title: "Empty cart",
        content: "<p>Are you sure you want to empty the cart?</p>",
        persistent: true,
        eventChannel: "cart-actions-empty-current-cart"
      },
      cartRestoreDialog: {
        icon: "mdi-recycle",
        titleCloseBtn: true,
        title: "Restore cart",
        component: "cartRestoreDialog",
        persistent: true,
        eventChannel: "cart-actions-restore-cart"
      }
    };
  },
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
    EventBus.$off();
  },

  computed: {
    checkoutColor() {
      switch (this.cart.shipping.method) {
        case "retail":
          return "primary";
        case "pickup":
          return "warning";
        case "delivery":
          return "purple";
      }
    },
    cart() {
      return this.$store.state.cart;
    }
  },

  methods: {
    ...mapMutations("cart", ["resetState", "setCheckoutDialog"]),
    ...mapMutations("dialog", ["setDialog"]),

    holdCart() {
      let product_count = Object.keys(this.cart.products).length;
      let payload = {
        model: "carts",
        data: {
          cash_register_id: this.$store.state.cashRegister.id,
          cart: {
            products: this.cart,
            customer_id: this.$store.state.cart.customer
              ? this.$store.state.cart.customer.id
              : null
          },
          discount_type: this.$store.state.cart.discount_type,
          discount_amount: this.$store.state.cart.discount_amount,
          shipping: { notes: this.$store.state.cart.shipping.notes },
          product_count: product_count,
          total_price: this.cart.cart_price
        }
      };
      this.create(payload).then(() => {
        this.getCartsOnHoldSize();
        this.resetState();

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
    },
    ...mapActions(["getAll", "create"]),
    ...mapActions("cart", ["submitOrder"])
  }
};
</script>
