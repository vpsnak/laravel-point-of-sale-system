<template>
  <v-card elevation="12">
    <v-tabs grow centered v-model="selected_tab">
      <v-tab>
        <v-icon left>mdi-clipboard-list-outline</v-icon>
        <span>Order summary & actions</span>
      </v-tab>
      <v-tab>
        <v-icon left>mdi-package-variant</v-icon>
        <span>
          Items <b class="primary--text">({{ cart_products.length }})</b>
        </span>
      </v-tab>
      <v-tab>
        <v-icon left>mdi-file-document-edit-outline</v-icon>
        <span>Order Options</span>
      </v-tab>

      <v-tabs-items v-model="selected_tab">
        <OrderSummaryTabItem />
        <OrderItemsTabItem />
        <OrderDeliveryOptionsTabItem />
      </v-tabs-items>
    </v-tabs>
  </v-card>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      selected_tab: null
    };
  },
  computed: {
    ...mapState("cart", ["cart_products"])
  }
};
</script>
