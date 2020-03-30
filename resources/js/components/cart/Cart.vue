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
    <v-container fluid class="pa-0">
      <cartProducts class="mt-2 elevation-2" :editable="$props.editable" />
    </v-container>

    <v-divider />

    <v-container class="py-0">
      <cartDiscount
        class="mt-2"
        :productIndex="-1"
        :editable="$props.editable"
      />
    </v-container>
    <cartTotals />

    <cartActions :disabled="totalProducts" v-if="$props.showActions" />

    <orderSave url="update-items" v-else-if="$props.showSave" />
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
    showSave: Boolean
  },

  computed: {
    ...mapState("cart", ["cart_products"]),

    totalProducts() {
      return _.size(this.cart_products) ? false : true;
    }
  }
};
</script>
