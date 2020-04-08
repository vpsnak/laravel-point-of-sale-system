<template>
  <v-card class="fill-height">
    <v-container fluid class="pa-0">
      <customerSearch
        v-if="$props.showCustomer"
        :editable="$props.editable"
        :showMethods="$props.showMethods"
        :keywordLength="3"
      />
      <v-divider v-if="$props.showCustomer" />
    </v-container>

    <cartProducts class="mt-2 elevation-2" :editable="$props.editable" />

    <v-divider />

    <v-container>
      <cartDiscount
        class="mt-1"
        :productIndex="-1"
        :editable="$props.editable"
      />
    </v-container>

    <cartTotals />

    <cartActions v-if="$props.showActions" :disabled="totalProducts" />

    <orderSave v-else-if="$props.showSave" url="update-items" />
  </v-card>
</template>

<script>
import { mapActions, mapState, mapMutations } from "vuex";

export default {
  props: {
    editable: Boolean,
    showMethods: Boolean,
    showCustomer: Boolean,
    showActions: Boolean,
    showSave: Boolean,
  },

  computed: {
    ...mapState("dialog", ["checkout_dialog"]),
    ...mapState("cart", ["cart_products"]),
    ...mapState("config", ["inner_height"]),

    totalProducts() {
      return _.size(this.cart_products) ? false : true;
    },
  },
};
</script>
