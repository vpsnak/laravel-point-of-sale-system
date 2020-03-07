<template>
  <v-menu
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <v-chip pill v-on="$props.menu ? on : null" color="teal">
        {{ parsePrice($props.totalPrice).toFormat() }}
      </v-chip>
    </template>
    <orderCostAnalysis />
  </v-menu>
</template>

<script>
import { mapMutations } from "vuex";
export default {
  props: {
    menu: Boolean,
    totalPrice: Object,
    mdsePrice: Object,
    taxPrice: Object,
    deliveryFeesPrice: Object
  },

  mounted() {
    this.setOrderTotalPrice(this.$props.totalPrice);
    this.setOrderMdsePrice(this.$props.mdsePrice);
    this.setOrderTaxPrice(this.$props.taxPrice);
    this.setDeliveryFeesPrice(this.$props.deliveryFeesPrice);
  },

  methods: {
    ...mapMutations("cart", [
      "setOrderTotalPrice",
      "setOrderMdsePrice",
      "setOrderTaxPrice",
      "setDeliveryFeesPrice"
    ])
  }
};
</script>
