<template>
  <v-menu
    v-model="orderCostAnalysis"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
    offset-y
  >
    <template v-slot:activator="{ on }">
      <v-chip
        label
        v-on="$props.menu ? on : null"
        color="primary"
        :small="small"
        dark
      >
        <b v-text="parsePrice($props.totalPrice).toFormat()" />
      </v-chip>
    </template>
    <orderCostAnalysis :orderPrices="orderPrices" />
  </v-menu>
</template>

<script>
import { mapMutations } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    small: Boolean,
    menu: Boolean,
    totalPrice: Object,
    mdsePrice: Object,
    taxPrice: Object,
    deliveryFeesPrice: Object
  },

  data() {
    return {
      orderCostAnalysis: false
    };
  },

  computed: {
    orderPrices() {
      return {
        totalPrice: this.$props.totalPrice,
        mdsePrice: this.$props.mdsePrice,
        taxPrice: this.$props.taxPrice,
        deliveryFeesPrice: this.$props.deliveryFeesPrice
      };
    }
  },

  beforeDestroy() {
    EventBus.$off("overlay");
  },

  watch: {
    orderCostAnalysis(value) {
      EventBus.$emit("overlay", value);
    }
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
