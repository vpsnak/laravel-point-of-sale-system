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
        <b>{{ parsePrice($props.totalPrice).toFormat() }}</b>
      </v-chip>
    </template>
    <orderCostAnalysis />
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

  mounted() {
    this.setOrderTotalPrice(this.$props.totalPrice);
    this.setOrderMdsePrice(this.$props.mdsePrice);
    this.setOrderTaxPrice(this.$props.taxPrice);
    this.setDeliveryFeesPrice(this.$props.deliveryFeesPrice);
  },

  beforeDestroy() {
    EventBus.$off("overlay");
  },

  data() {
    return {
      orderCostAnalysis: false
    };
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
