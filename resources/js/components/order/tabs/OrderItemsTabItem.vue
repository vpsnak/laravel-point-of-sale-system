<template>
  <v-tab-item>
    <v-card outlined>
      <v-card-title>
        <v-icon left>mdi-information-variant</v-icon>
        <span class="subheading">
          Items list
        </span>
      </v-card-title>
      <v-container fluid>
        <v-row no-gutters justify="center">
          <v-col :cols="6" v-for="product in cart_products" :key="product.id">
            <v-banner single-line>
              <v-row justify="center">
                <v-col>
                  <v-avatar size="75" tile>
                    <v-img
                      :lazy-src="product.photo_url"
                      :src="product.photo_url"
                    />
                  </v-avatar>
                </v-col>
                <v-col>
                  <span class="subheading">{{ product.name }}</span>
                </v-col>
              </v-row>

              <template v-slot:actions>
                <v-btn text icon><v-icon>mdi-eye</v-icon></v-btn>
                <v-btn text color="deep-purple accent-4">Edit</v-btn>
              </template>
            </v-banner>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-tab-item>
</template>

<script>
import { mapState } from "vuex";

export default {
  data() {
    return {
      edit_products: false
    };
  },
  computed: {
    ...mapState("cart", ["cart_products"])
    // configurable price, qty, add / remove product, discounts, save btn, close confirmation if unsaved changes are detected, validator
  },
  methods: {
    save() {
      this.edit_products = false;
    }
  }
};
</script>
