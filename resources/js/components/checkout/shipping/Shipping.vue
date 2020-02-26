<template>
  <v-row justify="center" align="center">
    <v-col :xl="8" :md="10" :cols="12">
      <component :is="template" />
    </v-col>
  </v-row>
</template>

<script>
import { mapState } from "vuex";

export default {
  computed: {
    ...mapState("cart", ["method"]),

    template() {
      switch (this.method) {
        default:
        case "retail":
          return "orderNotes";
        case "pickup":
          return "inStorePickupOption";
        case "delivery":
          return "deliveryOption";
      }
    }
  }
};
</script>
