<template>
  <v-card class="pa-3">
    <v-container>
      <v-row dense align="center" justify="space-between">
        <customerSearch
          v-if="$props.showCustomer"
          :editable="$props.editable"
          :keywordLength="3"
        />
        <cartMethods v-if="$props.showMethods" />
      </v-row>

      <v-divider v-if="$props.showMethods" />

      <v-divider v-if="$props.showCustomer" />

      <cartProducts :editable="$props.editable" />

      <cartDiscount :productIndex="-1" :editable="$props.editable" />

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
    title: String,
    icon: String,
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
