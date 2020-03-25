<template>
  <ValidationObserver tag="v-form" @input="isValidCart()">
    <v-container style="height:41vh; overflow-y:auto">
      <v-row>
        <v-col>
          <v-expansion-panels accordion>
            <v-expansion-panel
              v-for="(product, index) in products"
              :key="product.id"
            >
              <v-expansion-panel-header @click.stop class="pa-1">
                <v-container fluid>
                  <v-row dense align="center">
                    <b class="primary--text">{{ 1 + index }}.</b>
                    <i class="mx-1">
                      <b>
                        {{ product.name }}
                      </b>
                    </i>
                    <v-spacer />
                    <span class="mx-1">
                      Price:
                      <i :class="parseProductPrice(product).class">
                        <b>
                          {{ parseProductPrice(product).label }}
                        </b>
                      </i>
                    </span>
                    <v-spacer />
                    <span class="mx-1">
                      Original price:
                      <i class="primary--text">
                        <b>
                          {{ $price(product.original_price).toFormat() }}
                        </b>
                      </i>
                    </span>
                  </v-row>

                  <v-row align="center">
                    <v-img
                      :src="product.photo_url"
                      :lazy-src="product.photo_url"
                      aspect-ratio="1"
                      class="grey lighten-2"
                      width="100%"
                      height="100%"
                      max-width="50"
                      max-height="50"
                    ></v-img>

                    <v-spacer />

                    <v-text-field
                      v-if="product.is_price_editable && editable"
                      class="mt-5 mx-1 pt-3"
                      style="max-width:125px;"
                      prefix="$"
                      @click.stop
                      @keyup.esc="revertPrice(index)"
                      @keyup.enter="setPrice(index, true)"
                      :ref="'priceField' + index"
                      :min="0"
                      type="number"
                      label="Edit price"
                      :readonly="!editPrice(index)"
                      outlined
                      :color="editPrice(index) ? 'primary' : ''"
                      :value="parsePrice(product.price).toFormat('0.00')"
                      dense
                      append-icon="mdi-pencil"
                      @click:append.stop="toggleEdit(index)"
                      persistent-hint
                      :hint="editPriceHint(index)"
                    ></v-text-field>

                    <v-text-field
                      v-else
                      class="mt-5 mx-1 pt-3"
                      style="max-width:125px;"
                      prefix="$"
                      :ref="'priceField' + index"
                      :min="0"
                      type="number"
                      label="Unit price"
                      :disabled="true"
                      outlined
                      :value="parsePrice(product.price).toFormat('0.00')"
                      dense
                      persistent-hint
                      :hint="editPriceHint(index)"
                    ></v-text-field>

                    <v-spacer />

                    <ValidationProvider
                      rules="required|min_value:1|max_value:500"
                      v-slot="{ errors }"
                      name="Qty"
                    >
                      <v-text-field
                        dense
                        outlined
                        class="mt-5 pt-3"
                        style="max-width:110px;"
                        :disabled="!editable"
                        type="number"
                        label="Qty"
                        v-model="product.qty"
                        :min="1"
                        @click.stop
                        prepend-inner-icon="remove"
                        @click:prepend-inner.stop="decreaseProductQty(product)"
                        append-icon="add"
                        @click:append.stop="increaseProductQty(product)"
                        persistent-hint
                        :hint="editPriceHint(null)"
                        :error="errors[0] ? true : false"
                      ></v-text-field>
                    </ValidationProvider>

                    <v-spacer />

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          class="mx-1"
                          v-if="editable"
                          @click.stop="viewProductDialog(product)"
                          v-on="on"
                          small
                          icon
                        >
                          <v-icon>mdi-eye</v-icon>
                        </v-btn>
                      </template>
                      <span>View item</span>
                    </v-tooltip>

                    <v-spacer />

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          class="mx-1"
                          v-if="editable"
                          @click.stop="removeProduct(index)"
                          color="red"
                          v-on="on"
                          small
                          icon
                        >
                          <v-icon>mdi-cart-remove</v-icon>
                        </v-btn>
                      </template>
                      <span>Remove from cart</span>
                    </v-tooltip>

                    <v-spacer />
                  </v-row>
                </v-container>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <cartDiscount
                  v-if="product.is_discountable"
                  :productIndex="index"
                  :productPrice="productPrice(product)"
                  :editable="editable"
                />
                <v-row>
                  <v-col :cols="12">
                    <v-textarea
                      dense
                      outlined
                      v-model="product.notes"
                      prepend-inner-icon="mdi-card-text-outline"
                      :rows="2"
                      label="Notes"
                      :hint="'For product: ' + product.name"
                      counter
                      no-resize
                      :disabled="!editable"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-col>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
import Dinero from "dinero.js";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: {
    editable: Boolean
  },

  computed: {
    ...mapState("cart", ["discountTypes", "cart_products"]),

    products: {
      get() {
        return this.cart_products;
      },
      set(value) {
        this.setCartProducts(value);
      }
    }
  },
  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", [
      "setCartProducts",
      "increaseProductQty",
      "decreaseProductQty",
      "isValidCart"
    ]),
    ...mapActions("cart", ["removeProduct"]),

    productPrice(product) {
      if (product.qty > 0) {
        return this.parsePrice(product.price).multiply(product.qty);
      } else {
        return this.parsePrice(product.price).multiply(0);
      }
    },
    editPriceHint(index) {
      if (this.editPrice(index)) {
        return "Enter to submit\nESC reverts price";
      } else {
        return `${_.repeat("\xa0", 15)}\n${_.repeat("\xa0", 18)}`;
      }
    },
    parseProductPrice(product) {
      const label = this.calcProductDiscount(product);
      const qty = product.qty ? Number.parseInt(product.qty) : 0;
      const priceBeforeDiscount = this.parsePrice(product.price).multiply(qty);

      if (label.isNegative() || label.isZero()) {
        return { class: "red--text", label: "Discount error" };
      } else {
        return {
          class: "primary--text",
          label: label.toFormat()
        };
      }
    },
    getSelectedInput(index) {
      return this.$refs[`priceField${index}`][0];
    },
    setPrice(index, toggleEdit = false) {
      if (
        !this.getSelectedInput(index).lazyValue ||
        this.getSelectedInput(index).lazyValue <= 0
      ) {
        this.getSelectedInput(index).lazyValue = this.originalPrice(
          index
        ).toFormat("0.00");
      }

      const price = this.getSelectedInput(index).lazyValue;

      this.$set(this.products[index], "price", {
        amount: Number.parseInt(price * 100)
      });
      if (toggleEdit) {
        this.toggleEdit(index);
      }
    },
    revertPrice(index) {
      this.getSelectedInput(index).lazyValue = null;
      this.setPrice(index, true);
      this.getSelectedInput(index).blur();
    },
    originalPrice(index) {
      return this.$price(this.products[index].original_price);
    },
    editPrice(index) {
      if (_.has(this.products[index], "editPrice")) {
        return this.products[index].editPrice;
      } else {
        return false;
      }
    },
    toggleEdit(index) {
      this.$set(
        this.products[index],
        "editPrice",
        !this.products[index].editPrice
      );

      if (this.editPrice(index)) {
        this.$nextTick(() => {
          this.getSelectedInput(index).focus();
        });
      } else {
        this.setPrice(index);
      }
    },
    viewProductDialog(product) {
      this.setDialog({
        show: true,
        width: 1000,
        title: "Cart item",
        titleCloseBtn: true,
        icon: "mdi-package-variant",
        component: "product",
        model: product
      });
    }
  }
};
</script>
