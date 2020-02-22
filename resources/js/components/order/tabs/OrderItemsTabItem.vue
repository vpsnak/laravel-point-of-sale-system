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
            :lg="4"
            :md="6"
            :cols="12"
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
                      SKU:
                      <i :class="valueColors">{{ product.sku }}</i>
                    </h4>
                  </v-col>
                  <v-col cols="auto">
                    <h4>
                      Original price:
                      <i :class="valueColors">
                        ${{ product.original_price }}
                      </i>
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
                          Price w/o discount:
                          <i :class="valueColors">${{ product.price }}</i>
                        </h4>
                        <h4>
                          Discount type:
                          <i :class="valueColors">
                            {{ product.discount_type }}
                          </i>
                        </h4>
                        <h4 v-if="product.discount_type === 'Flat'">
                          Discount amount:
                          <i :class="valueColors">
                            ${{ product.discount_amount }}
                          </i>
                        </h4>
                        <h4 v-else>
                          Discount %:
                          <i :class="valueColors">
                            {{ product.discount_amount }}
                          </i>
                        </h4>
                      </v-col>
                      <v-col cols="auto">
                        <h4>
                          Qty ordered:
                          <i :class="valueColors">
                            {{ product.qty }}
                          </i>
                        </h4>
                        <h4 v-if="hasDiscount(product)">
                          Sale price w/ discount:
                          <i :class="valueColors">
                            ${{ product.final_price }}
                          </i>
                        </h4>
                        <h4 v-else>
                          Sale price:
                          <i :class="valueColors">
                            ${{ product.final_price }}
                          </i>
                        </h4>
                        <h4>
                          Total:
                          <i :class="valueColors">
                            ${{ product.qty * product.final_price }}
                          </i>
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
                        <v-btn icon v-on="on" @click="viewProduct(product)">
                          <v-icon>mdi-eye</v-icon>
                        </v-btn>
                      </template>
                      <span>View</span>
                    </v-tooltip>
                  </v-col>

                  <v-col cols="auto" v-if="$props.editable">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on" @click="toggleEdit(index)">
                          <v-icon>edit</v-icon>
                        </v-btn>
                      </template>
                      <span>Edit</span>
                    </v-tooltip>
                  </v-col>

                  <v-col cols="auto">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-badge
                          color="pink"
                          dot
                          :value="hasNotes(product)"
                          overlap
                        >
                          <v-btn
                            icon
                            v-on="on"
                            @click="viewNotes(product)"
                            :disabled="!hasNotes(product)"
                          >
                            <v-icon>mdi-card-text-outline</v-icon>
                          </v-btn>
                        </v-badge>
                      </template>
                      <span>View notes</span>
                    </v-tooltip>
                  </v-col>

                  <v-col cols="auto" v-if="$props.editable">
                    <v-tooltip bottom color="red">
                      <template v-slot:activator="{ on }">
                        <v-btn
                          icon
                          color="red"
                          v-on="on"
                          @click="removeProduct(index)"
                        >
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
import { mapState, mapMutations } from "vuex";

export default {
  props: {
    editable: Boolean
  },

  computed: {
    ...mapState("cart", ["cart_products"]),
    // configurable price, qty, add / remove product, discounts, save btn, close confirmation if unsaved changes are detected, validator

    valueColors() {
      return "primary--text";
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),

    toggleEdit(index) {},
    viewProduct(product) {
      this.setDialog({
        show: true,
        title: `View: ${product.name}`,
        component: "product",
        icon: "mdi-package-variant",
        model: product,
        width: 1000,
        titleCloseBtn: true
      });
    },
    viewNotes(product) {
      this.setDialog({
        show: true,
        title: `Notes: ${product.name}`,
        component: "orderItemNotes",
        icon: "mdi-card-text-outline",
        model: product.notes,
        width: 1000,
        titleCloseBtn: true
      });
    },
    hasNotes(product) {
      if (product.notes && product.notes.length > 0) {
        return true;
      } else {
        return false;
      }
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
