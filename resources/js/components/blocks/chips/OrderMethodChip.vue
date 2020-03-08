<template>
  <v-menu
    v-model="methodDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title">Type</h5>
      <v-chip
        pill
        v-on="$props.method === 'retail' || !$props.menu ? '' : on"
        :color="parseMethod.color"
        :small="small"
      >
        <v-icon left>{{ parseMethod.icon }}</v-icon>
        {{ parseMethod.name }}
      </v-chip>
    </template>
    <v-card class="pa-5" outlined width="600" shaped>
      <component
        :is="parseMethod.component"
        :model="parseMethod.model"
        :readonly="true"
        :hideNotes="true"
      />
    </v-card>
  </v-menu>
</template>
<script>
export default {
  props: {
    small: Boolean,
    title: Boolean,
    menu: Boolean,
    method: String
  },

  data() {
    return {
      methodDetails: false
    };
  },

  computed: {
    parseMethod() {
      switch (this.$props.method) {
        default:
        case "retail":
          return {
            name: "Cash & Carry",
            component: null,
            icon: "mdi-cart-arrow-right",
            color: "primary",
            model: null
          };
        case "pickup":
          return {
            name: "Store pickup",
            component: "inStorePickupOption",
            icon: "mdi-storefront",
            color: "warning",
            model: "order_delivery_store_pickup"
          };
        case "delivery":
          return {
            name: "Delivery",
            component: "deliveryOption",
            icon: "mdi-truck-delivery",
            color: "purple",
            model: "order_delivery_address"
          };
      }
    }
  }
};
</script>
