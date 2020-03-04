<template>
  <v-card class="pa-3">
    <div class="d-flex">
      <div class="d-flex align-center justify-center">
        <v-icon class="pr-2">{{ icon }}</v-icon>
        <h4 class="title-2">{{ title }}</h4>
      </div>

      <v-spacer />

      <cartMethods v-if="$props.showMethods" />
    </div>

    <v-divider class="py-1" v-if="$props.showMethods" />

    <customerSearch
      v-if="$props.showCustomer"
      :editable="$props.editable"
      :keywordLength="3"
      class="pa-3"
    />

    <v-divider class="py-1" v-if="$props.showCustomer" />

    <cartProducts :editable="$props.editable" />

    <v-divider />

    <div class="d-flex flex-column">
      <v-row class="d-flex justify-start align-center">
        <v-col :cols="4" class="px-5 py-0">
          <v-label>Cart discount</v-label>
        </v-col>
        <v-col :cols="4" class="px-2 py-0">
          <cartDiscount :product_index="-1" :editable="$props.editable" />
        </v-col>
      </v-row>

      <v-divider />

      <cartTotals />

      <cartActions v-if="$props.showActions" :disabled="totalProducts" />
      <orderSave v-else-if="$props.showSave" url="update-items" />
    </div>
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
