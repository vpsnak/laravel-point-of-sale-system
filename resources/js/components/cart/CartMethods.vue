<template>
  <v-btn-toggle
    v-model="shippingMethod"
    mandatory
    group
    @change="resetDelivery()"
  >
    <v-tooltip bottom>
      <template v-slot:activator="{ on }">
        <v-btn
          v-if="!$props.editOrder"
          color="primary"
          icon
          value="retail"
          v-on="on"
          :disabled="isRetailDisabled"
        >
          <v-icon v-text="'mdi-cart-arrow-right'" />
        </v-btn>
      </template>
      <span v-text="'Cash & Carry'" />
    </v-tooltip>
    <v-tooltip bottom>
      <template v-slot:activator="{ on }">
        <v-btn
          color="warning"
          icon
          value="pickup"
          :disabled="isNonRetailDisabled"
          v-on="on"
        >
          <v-icon v-text="'mdi-storefront'" />
        </v-btn>
      </template>
      <span v-text="'In Store Pickup'" />
    </v-tooltip>
    <v-tooltip bottom>
      <template v-slot:activator="{ on }">
        <v-btn
          color="purple"
          icon
          value="delivery"
          :disabled="isNonRetailDisabled"
          v-on="on"
        >
          <v-icon v-text="'mdi-truck-delivery'" />
        </v-btn>
      </template>
      <span v-text="'Delivery'" />
    </v-tooltip>
  </v-btn-toggle>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
  props: {
    editOrder: Boolean
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "customer",
      "delivery",
      "method",
      "cart_products"
    ]),

    isRetailDisabled() {
      return this.order_id ? true : false;
    },

    isNonRetailDisabled() {
      if (
        (this.$props.editOrder && this.customer) ||
        (!this.$props.editOrder && this.customer && !this.order_id)
      ) {
        return false;
      } else {
        return true;
      }
    },

    shippingMethod: {
      get() {
        return this.method;
      },
      set(value) {
        this.setMethod(value);
        this.setMethodStep();
      }
    }
  },

  methods: {
    ...mapMutations("cart", ["setMethod", "setMethodStep", "resetDelivery"])
  }
};
</script>
