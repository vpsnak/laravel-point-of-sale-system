<template>
  <v-menu
    v-model="orderIncomeAnalysis"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
    offset-y
  >
    <template v-slot:activator="{ on }">
      <v-chip
        pill
        v-on="$props.menu ? on : null"
        color="primary"
        :small="small"
      >
        <b>{{ parsePrice($props.income_price).toFormat() }}</b>
      </v-chip>
    </template>
    <orderIncomeAnalysis :orderId="$props.orderId" />
  </v-menu>
</template>

<script>
import { mapActions } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    small: Boolean,
    menu: Boolean,
    orderId: Number,
    income_price: Object
  },

  data() {
    return {
      orderIncomeAnalysis: false
    };
  },

  beforeDestroy() {
    EventBus.$off("overlay");
  },

  watch: {
    orderIncomeAnalysis(value) {
      EventBus.$emit("overlay", value);
    }
  }
};
</script>
