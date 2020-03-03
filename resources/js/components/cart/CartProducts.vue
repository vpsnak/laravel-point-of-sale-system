<template>
  <div class="d-flex flex-grow-1" style="height:38vh; overflow-y:auto">
    <v-expansion-panels class="d-block" accordion>
      <v-expansion-panel v-for="(product, index) in products" :key="index">
        <v-expansion-panel-header class="pa-2" ripple @click.stop>
          <div class="d-flex flex-column" v-if="product.photo_url">
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
          </div>
          <div class="d-flex justify-space-around">
            <div class="d-flex flex-column">
              <span class="pb-1">
                {{ product.name }}
              </span>
              <div style="width:100%; max-width:150px;">
                <v-text-field
                  prefix="$"
                  @click.stop
                  single-line
                  @keyup.esc="revertPrice(index)"
                  @keyup.enter="setPrice(index, null, true)"
                  :ref="'priceField' + index"
                  :min="0"
                  type="number"
                  :readonly="!editPrice(index)"
                  :flat="!editPrice(index)"
                  :outlined="editPrice(index)"
                  :solo="!editPrice(index)"
                  :color="editPrice(index) ? 'yellow' : ''"
                  :value="displayPriceNoSign(product.price)"
                  :hint="
                    'Original price: ' + displayPrice(product.original_price)
                  "
                  dense
                ></v-text-field>
              </div>
            </div>

            <v-spacer />

            <div class="d-flex justify-content-center align-center">
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn
                    v-if="
                      product.editable_price &&
                        editable &&
                        !product.sku.startsWith('giftCard')
                    "
                    :color="editPrice(index) ? 'yellow' : ''"
                    :input-value="editPrice(index) ? true : false"
                    @click.stop="toggleEdit(index)"
                    icon
                    v-on="on"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                </template>
                <span>Edit price</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn
                    class="mx-2"
                    v-if="
                      editable &&
                        !(
                          product.sku.startsWith('dummy') ||
                          product.sku.startsWith('giftCard')
                        )
                    "
                    icon
                    @click.stop="viewProductDialog(product)"
                    v-on="on"
                  >
                    <v-icon>mdi-eye</v-icon>
                  </v-btn>
                </template>
                <span>View item</span>
              </v-tooltip>
              <v-tooltip bottom color="red">
                <template v-slot:activator="{ on }">
                  <v-btn
                    color="red"
                    v-on="on"
                    v-if="editable"
                    class="mx-2"
                    icon
                    @click.stop="decreaseProductQty(product)"
                  >
                    <v-icon>remove</v-icon>
                  </v-btn>
                </template>
                <span>Decrease qty</span>
              </v-tooltip>
              <v-text-field
                style="max-width:45px;"
                :disabled="!editable"
                type="number"
                label="Qty"
                v-model="product.qty"
                :min="1"
                @click.stop
                @blur="limits(product)"
                @keyup="limits(product)"
              ></v-text-field>

              <v-tooltip bottom color="green">
                <template v-slot:activator="{ on }">
                  <v-btn
                    color="green"
                    v-on="on"
                    icon
                    class="mx-2"
                    v-if="editable"
                    @click.stop="increaseProductQty(product)"
                  >
                    <v-icon>add</v-icon>
                  </v-btn>
                </template>
                <span>Increase qty</span>
              </v-tooltip>

              <v-tooltip bottom color="red">
                <template v-slot:activator="{ on }">
                  <v-btn
                    class="mx-2"
                    v-if="editable"
                    icon
                    @click.stop="removeItem(index)"
                    color="red"
                    v-on="on"
                  >
                    <v-icon>delete</v-icon>
                  </v-btn>
                </template>
                <span>Remove from cart</span>
              </v-tooltip>
            </div>
          </div>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <cartDiscount
            :product_index="index"
            :product_price="multiplyPrice(product.price, product.qty)"
            :editable="editable"
          ></cartDiscount>
          <v-row>
            <v-col :cols="12">
              <v-textarea
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
  </div>
</template>

<script>
import Dinero from "dinero.js";
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";

export default {
  props: {
    editable: Boolean
  },
  computed: {
    ...mapState("cart", ["discountTypes", "cart_products"]),
    ...mapGetters("price", [
      "parsePrice",
      "displayPrice",
      "displayPriceNoSign",
      "multiplyPrice"
    ]),

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
      "decreaseProductQty"
    ]),

    cancelEvent(index, event) {
      if (!this.editPrice(index)) {
        event.preventDefault();
      }
    },
    getSelectedInput(index) {
      return this.$refs[`priceField${index}`][0];
    },
    setPrice(index, price = null, toggleEdit = false) {
      if (!this.getSelectedInput(index).lazyValue) {
        this.getSelectedInput(index).lazyValue = this.originalPrice(
          index
        ).toFormat("0,0.00");
      }
      if (price) {
        this.products[index].price.amount = _.toInteger(price * 100);
      } else {
        this.products[index].price.amount = _.toInteger(
          this.getSelectedInput(index).lazyValue * 100
        );
      }
      if (toggleEdit) {
        this.toggleEdit(index);
      }
    },
    revertPrice(index) {
      this.getSelectedInput(index).lazyValue = null;
      this.setPrice(index, null, true);
      this.getSelectedInput(index).blur();
    },
    originalPrice(index) {
      return this.parsePrice(this.products[index].original_price);
    },
    editPrice(index) {
      if (_.has(this.products[index], "editPrice")) {
        return this.products[index].editPrice;
      } else {
        return false;
      }
    },
    toggleEdit(index) {
      Vue.set(
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
    },
    limits(product) {
      if (product.qty < 1) {
        product.qty = 1;
      }
    },
    removeItem(index) {
      this.products.splice(index, 1);
    }
  }
};
</script>
