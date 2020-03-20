<template>
  <v-card class="fill-height">
    <v-container fluid>
      <customerSearch
        v-if="$props.showCustomer"
        :editable="$props.editable"
        :showMethods="$props.showMethods"
        :keywordLength="3"
      />
      <v-divider v-if="$props.showCustomer" />
    </v-container>
    <v-container>
      <cartProducts :editable="$props.editable" />

      <cartDiscount
        class="mt-2"
        :productIndex="-1"
        :editable="$props.editable"
      />

      <cartTotals />

      <cartActions :disabled="totalProducts" v-if="$props.showActions" />

      <orderSave url="update-items" v-else-if="$props.showSave" />
    </v-container>
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
