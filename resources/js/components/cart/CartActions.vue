<template>
  <v-container class="pb-0">
    <v-row justify="center" class="px-5">
      <v-btn
        outlined
        dark
        :color="checkoutColor"
        block
        class="my-2"
        @click.stop="setCheckoutDialog(true)"
        :disabled="disableCheckout"
        v-text="'checkout'"
      />
    </v-row>
    <v-row align="center" justify="space-around">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            fab
            @click="setDialog(cartRestoreDialog)"
            color="green"
            v-on="on"
            :disabled="!cartsOnHoldSize"
          >
            <v-icon v-text="'mdi-recycle'" />
            <v-badge
              :value="cartsOnHoldSize > 0 ? true : false"
              color="deep-orange"
              :content="cartsOnHoldSize"
            />
          </v-btn>
        </template>
        <b v-text="'Restore cart'" />
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            fab
            :disabled="disabled"
            @click="holdCart"
            color="orange"
            v-on="on"
          >
            <v-icon v-text="'pause'" />
          </v-btn>
        </template>
        <b v-text="'Hold current cart'" />
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            fab
            @click.stop="setDialog(emptyCartDialog)"
            :disabled="disabled"
            color="red"
            v-on="on"
          >
            <v-icon v-text="'mdi-delete-outline'" />
          </v-btn>
        </template>
        <b v-text="'Empty current cart'" />
      </v-tooltip>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../plugins/eventBus";

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
      cartsOnHoldSize: null,

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
      "productMap",
      "customer",
      "method",
      "delivery_fees_price",
      "cart_products",
      "order_discount",
      "delivery",
      "billing_address_id",
      "order_notes",
      "order_billing_address",
      "order_delivery_address",
      "order_delivery_store_pickup",
      "notes",
      "order_total_price"
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
    },
    disableCheckout() {
      const orderTotalPrice = this.parsePrice(this.order_total_price);

      if (
        !this.$props.disabled &&
        this.isValidCheckout &&
        orderTotalPrice.greaterThan(this.parsePrice())
      ) {
        return false;
      } else {
        return true;
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
    ...mapActions("requests", ["request"]),
    ...mapActions("cart", ["submitOrder"]),

    holdCart() {
      const payload = {
        method: "post",
        url: "carts/create",
        data: {
          cart: {
            productMap: this.productMap,
            customer: this.customer,
            method: this.method,
            delivery_fees_price: this.delivery_fees_price,
            cart_products: this.cart_products,

            order_discount: this.order_discount,

            delivery: this.delivery,

            billing_address_id: this.billing_address_id,

            // order_total_price,
            // order_mdse_price,
            // order_tax_price,

            // order_remaining_price,
            order_notes: this.order_notes,
            order_billing_address: this.order_billing_address,
            order_delivery_address: this.order_delivery_address,
            order_delivery_store_pickup: this.order_delivery_store_pickup
          }
        }
      };
      this.request(payload).then(response => {
        this.cartsOnHoldSize = response.count;
        this.resetState();
      });
    },
    getCartsOnHoldSize() {
      const payload = {
        method: "get",
        url: "carts/count"
      };
      this.request(payload).then(response => {
        this.cartsOnHoldSize = response;
      });
    }
  }
};
</script>
