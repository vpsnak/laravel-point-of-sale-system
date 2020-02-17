<template>
  <v-tab-item>
    <v-card outlined>
      <v-card-title>
        <v-icon left>mdi-view-list</v-icon>
        <span class="subheading">
          Item list
        </span>
      </v-card-title>
      <v-container fluid class="overflow-y-auto" style="max-height:80vh">
        <v-row dense align="center">
          <v-col
            :md="4"
            :cols="6"
            v-for="(product, index) in cart_products"
            :key="index"
          >
            <v-card class="d-inline-block mx-auto" width="100%" outlined>
              <v-container>
                <v-row align="center" justify="space-around">
                  <v-col cols="auto">
                    <h3>
                      <b class="amber--text">{{ 1 + index }}.</b>
                      &nbsp;{{ product.name }}
                    </h3>
                  </v-col>
                  <v-col cols="auto">
                    <h4>
                      <i>SKU:</i>
                      <span :class="valueColors">{{ product.sku }}</span>
                    </h4>
                  </v-col>
                  <v-col cols="auto">
                    <h4>
                      <i>Original price:</i>
                      <span :class="valueColors">
                        ${{ product.original_price }}
                      </span>
                    </h4>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="auto">
                    <v-row class="ma-0" justify="space-around">
                      <v-col>
                        <v-img
                          :lazy-src="product.photo_url"
                          :src="product.photo_url"
                          width="75"
                          height="75"
                        />
                      </v-col>
                      <v-col cols="auto" v-if="hasDiscount(product)">
                        <h4 v-if="hasDiscount">
                          <i>Price w/o discount:</i>
                          <span :class="valueColors">${{ product.price }}</span>
                        </h4>
                        <h4>
                          <i>Discount type:</i>
                          <span :class="valueColors">
                            {{ product.discount_type }}
                          </span>
                        </h4>
                        <h4 v-if="product.discount_type === 'Flat'">
                          <i>Discount amount:</i>
                          <span :class="valueColors">
                            ${{ product.discount_amount }}
                          </span>
                        </h4>
                        <h4 v-else>
                          <i>Discount %:</i>
                          <span :class="valueColors">
                            {{ product.discount_amount }}
                          </span>
                        </h4>
                      </v-col>
                      <v-col cols="auto">
                        <h4>
                          <i>Qty ordered:</i>
                          <span :class="valueColors">
                            {{ product.qty }}
                          </span>
                        </h4>
                        <h4 v-if="hasDiscount(product)">
                          <i>Sale price w/ discount:</i>
                          <span :class="valueColors">
                            ${{ product.final_price }}
                          </span>
                        </h4>
                        <h4 v-else>
                          <i>Sale price:</i>
                          <span :class="valueColors">
                            ${{ product.final_price }}
                          </span>
                        </h4>
                        <h4>
                          <i>Total:</i>
                          <span :class="valueColors">
                            ${{ product.qty * product.final_price }}
                          </span>
                        </h4>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-row
                  class="ma-0"
                  justify="space-around"
                  align="center"
                  no-gutters
                >
                  <v-col cols="auto">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon>mdi-eye</v-icon>
                        </v-btn>
                      </template>
                      <span>View</span>
                    </v-tooltip>
                  </v-col>

                  <v-col cols="auto">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon>edit</v-icon>
                        </v-btn>
                      </template>
                      <span>Edit</span>
                    </v-tooltip>
                  </v-col>

                  <v-col cols="auto">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                          <v-icon>mdi-card-text-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>View notes</span>
                    </v-tooltip>
                  </v-col>

                  <v-col cols="auto">
                    <v-tooltip bottom color="red">
                      <template v-slot:activator="{ on }">
                        <v-btn icon color="red" v-on="on">
                          <v-icon>mdi-delete-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Remove from order</span>
                    </v-tooltip>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
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
    ...mapState("cart", ["cart_products"]),
    // configurable price, qty, add / remove product, discounts, save btn, close confirmation if unsaved changes are detected, validator

    valueColors() {
      return "primary--text";
    }
  },
  methods: {
    save() {
      this.edit_products = false;
    },
    hasDiscount(product) {
      if (
        product.discount_amount > 0 &&
        product.discount_type &&
        _.lowerCase(product.discount_type) !== "none"
      ) {
        return true;
      } else {
        return false;
      }
    }
  }
};
</script>
