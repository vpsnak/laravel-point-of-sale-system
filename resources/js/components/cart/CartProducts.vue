<template>
  <ValidationObserver tag="v-form" @input="isValidCart()">
    <perfect-scrollbar
      style="height: 450px;"
      tag="v-container"
      fluid
      class="py-0"
      ref="scroll"
    >
      <v-row dense ref="cart">
        <v-col :cols="12">
          <v-expansion-panels accordion>
            <v-expansion-panel
              v-for="(product, index) in products"
              :key="product.id"
              class="elevation-8"
            >
              <v-expansion-panel-header class="px-2 py-0" @click.stop>
                <v-container fluid>
                  <v-row dense align="center">
                    <v-col :cols="4">
                      <b class="amber--text mb-1" v-text="`${1 + index}.`" />
                      <i>
                        <b class="primary--text" v-text="product.name" />
                        <b
                          v-if="product.type === 'giftcard'"
                          class="amber--text"
                          v-text="`- ${product.code}`"
                        />
                        <p
                          class="ml-5 mt-1 mb-0"
                          v-text="`(${product.type})`"
                        />
                      </i>
                    </v-col>
                    <v-col :cols="4">
                      <span>
                        Price:
                        <i :class="parseProductPrice(product).class">
                          <b v-text="parseProductPrice(product).label" />
                        </i>
                      </span>
                    </v-col>

                    <v-col :cols="4">
                      <span>
                        Original price:
                        <i class="primary--text">
                          <b
                            v-text="
                              parsePrice(product.original_price).toFormat()
                            "
                          />
                        </i>
                      </span>
                    </v-col>
                  </v-row>

                  <v-row align="center">
                    <div
                      class="d-flex justify-center align-center"
                      style="width: 50px; height: 50px;"
                    >
                      <v-img
                        v-if="product.type === 'product' && product.photo_url"
                        :src="product.photo_url"
                        @error="product.photo_url = null"
                        aspect-ratio="1"
                        width="100%"
                        height="100%"
                        contain
                      />

                      <v-icon
                        v-else-if="product.type === 'custom item'"
                        size="50px"
                        v-text="'mdi-flower'"
                      />

                      <v-icon
                        v-else-if="product.type === 'giftcard'"
                        size="50px"
                        v-text="'mdi-wallet-giftcard'"
                      />

                      <v-icon
                        v-else
                        size="50px"
                        v-text="'mdi-image-off-outline'"
                      />
                    </div>

                    <v-spacer />

                    <v-text-field
                      v-if="product.is_price_editable && $props.editable"
                      class="mt-5 mx-1 pt-3"
                      style="max-width: 125px;"
                      prefix="$"
                      @click.stop
                      @keyup.esc="revertPrice(index)"
                      @keyup.enter="setPrice(index, true)"
                      :ref="`priceField${index}`"
                      :min="0"
                      type="number"
                      label="Edit price"
                      :readonly="!editPrice(index)"
                      outlined
                      :value="parsePrice(product.price).toFormat('0.00')"
                      dense
                      append-icon="mdi-pencil"
                      @click:append.stop="toggleEdit(index)"
                      persistent-hint
                      :hint="editPriceHint(index)"
                    />

                    <v-text-field
                      v-else
                      class="mt-5 mx-1 pt-3"
                      style="max-width: 125px;"
                      prefix="$"
                      :ref="`priceField${index}`"
                      :min="0"
                      type="number"
                      label="Unit price"
                      :disabled="true"
                      outlined
                      :value="parsePrice(product.price).toFormat('0.00')"
                      dense
                      persistent-hint
                      :hint="editPriceHint(index)"
                    />

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
                        :style="
                          `max-width:110px;${
                            product.type === 'giftcard'
                              ? 'visibility:hidden;'
                              : ''
                          }`
                        "
                        :disabled="
                          !$props.editable || product.type === 'giftcard'
                        "
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
                      />
                    </ValidationProvider>

                    <v-spacer />

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          class="mx-1"
                          v-if="$props.editable"
                          @click.stop="viewItemDialog(product)"
                          v-on="on"
                          small
                          icon
                          :disabled="product.type === 'custom'"
                        >
                          <v-icon v-text="'mdi-eye'" />
                        </v-btn>
                      </template>
                      <span v-text="'View item'" />
                    </v-tooltip>

                    <v-spacer />

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          class="mx-1"
                          v-if="$props.editable"
                          @click.stop="removeProduct(index)"
                          color="red"
                          v-on="on"
                          small
                          icon
                        >
                          <v-icon v-text="'mdi-delete-outline'" />
                        </v-btn>
                      </template>
                      <span v-text="'Remove from cart'" />
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
                  :editable="$props.editable"
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
                      :hint="`for ${product.type}: ${product.name}`"
                      counter
                      no-resize
                      :disabled="!$props.editable"
                    />
                  </v-col>
                </v-row>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-col>
      </v-row>
    </perfect-scrollbar>
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

  watch: {
    cart_products() {
      this.$nextTick(() => {
        this.$refs.scroll.$el.scrollTop = this.$refs.cart.clientHeight;
      });
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
    viewItemDialog(item) {
      const payload = {
        show: true,
        width: item.type === "giftcard" ? 450 : 1000,
        title:
          item.type === "giftcard"
            ? `Giftcard: ${item.code}`
            : `Product: ${item.sku}`,
        titleCloseBtn: true,
        icon: item.type === "giftcard" ? "mdi-wallet-giftcard" : "mdi-flower",
        component: item.type === "giftcard" ? "giftCardForm" : `product`,
        component_props: { model: item }
      };
      this.setDialog(payload);
    }
  }
};
</script>
