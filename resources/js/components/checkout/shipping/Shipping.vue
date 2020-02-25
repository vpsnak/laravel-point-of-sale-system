<template>
  <component :is="template" />
</template>

<script>
import { mapState } from "vuex";

export default {
  computed: {
    ...mapState("cart", ["method"]),

    template() {
      switch (this.method) {
        case "delivery":
          return "deliveryOption";
        case "pickup":
          return "inStorePickupOption";
        case "retail":
          return "orderNotes";
        default:
          return "orderNotes";
      }
    }
  }
};
</script>
