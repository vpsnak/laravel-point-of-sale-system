<template>
  <v-menu
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <v-chip
        pill
        v-on="$props.menu ? on : null"
        color="primary"
        :small="small"
      >
        {{ parsePrice($props.paid_price).toFormat() }}
      </v-chip>
    </template>
    <orderPaymentAnalysis :orderId="$props.orderId" />
  </v-menu>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    small: Boolean,
    menu: Boolean,
    orderId: Number,
    paid_price: Object
  }
};
</script>
