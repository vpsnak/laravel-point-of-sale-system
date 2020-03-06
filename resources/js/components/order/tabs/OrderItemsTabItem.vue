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
            :key="product.id"
          >
            <v-card class="d-inline-block mx-auto" width="100%" outlined>
              <v-container>
                <v-row align="center" justify="space-around">
                  <v-col cols="auto">
                    <h4>
                      <b class="amber--text">{{ 1 + index }}.</b>
                      &nbsp;{{ product.name }}
                    </h4>
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
                        {{ originalPrice(product).toFormat() }}
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
                      <v-col cols="auto" v-if="productHasDiscount(product)">
                        <h4>
                          Price w/o discount:
                          <i :class="valueColors">
                            {{ parsePrice(product.price).toFormat() }}
                          </i>
                        </h4>
                        <h4>
                          Discount type:
                          <i :class="valueColors">
                            {{ product.discount.type }}
                          </i>
                        </h4>
                        <h4>
                          <span
                            v-if="product.discount.type === 'flat'"
                            v-html="'Discount amount:'"
                          />
                          <span v-else v-html="'Discount %:'" />
                          <i :class="valueColors">
                            {{ discountAmount(product) }}
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
                        <h4>
                          <span
                            v-if="productHasDiscount(product)"
                            v-html="'Sale price w/ discount:'"
                          />
                          <span v-else v-html="'Sale price:'" />
                          <i :class="valueColors">
                            {{ salePrice(product).toFormat() }}
                          </i>
                        </h4>
                        <h4>
                          Total:
                          <i :class="valueColors">
                            {{ totalPrice(product).toFormat() }}
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
  computed: {
    ...mapState("cart", ["cart_products"]),

    valueColors() {
      return "primary--text";
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),

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
      if (product.notes && product.notes.length) {
        return true;
      } else {
        return false;
      }
    },

    discountAmount(product) {
      if (product.discount.type === "flat") {
        return this.$price(product.discount).toFormat();
      } else {
        return product.discount.amount;
      }
    },
    originalPrice(product) {
      return this.parsePrice(product.original_price);
    },
    salePrice(product) {
      return this.calcProductDiscount(product);
    },
    totalPrice(product) {
      return this.salePrice(product).multiply(product.qty);
    }
  }
};
</script>
