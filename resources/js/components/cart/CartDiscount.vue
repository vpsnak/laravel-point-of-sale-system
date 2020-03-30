<template>
  <ValidationObserver ref="checkoutObs" slim>
    <v-row dense align="center">
      <v-col :cols="6">
        <v-select
          outlined
          @change="setDiscountType($event)"
          v-if="$props.editable"
          :value="discount.type"
          item-text="text"
          item-value="value"
          :items="discountTypes"
          :label="discountLabel"
          dense
        ></v-select>
        <v-text-field
          v-else
          outlined
          :value="discount.type"
          :label="discountLabel"
          disabled
          dense
        ></v-text-field>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider
          v-slot="{ errors }"
          :rules="`between:${0},${max}`"
          name="Discount amount"
          v-if="$props.editable"
        >
          <v-text-field
            ref="amount"
            v-model="discountAmount"
            type="number"
            label="Amount"
            :min="0"
            :max="max"
            :error-messages="errors[0] ? 'Invalid amount' : undefined"
            :disabled="enableAmount"
            dense
            outlined
          ></v-text-field>
        </ValidationProvider>
        <v-text-field
          v-else
          :value="discountAmount"
          label="Amount"
          :disabled="true"
          dense
          outlined
        ></v-text-field>
      </v-col>
    </v-row>
  </ValidationObserver>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  props: {
    productPrice: Object,
    productIndex: Number,
    editable: Boolean
  },

  data() {
    return {
      discount_amount: null
    };
  },

  mounted() {
    if (this.discount.amount) {
      switch (this.discount.type) {
        case "flat":
          this.discountAmount = Number.parseInt(this.discount.amount / 100);
          break;
        case "percentage":
          this.discountAmount = this.discount.amount;
          break;
        default:
          this.discountAmount = null;
          break;
      }
    } else {
      this.discountAmount = null;
    }
  },

  watch: {
    productPrice() {
      this.$nextTick(() => {
        this.runValidation();
      });
    },
    order_total_price() {
      this.$nextTick(() => {
        this.runValidation();
      });
    }
  },

  computed: {
    ...mapState("cart", [
      "product_map",
      "order_total_price",
      "discountTypes",
      "order_id",
      "cart_products",
      "order_discount",
      "discount_error"
    ]),

    discountLabel() {
      return this.productIndex === -1 ? "Cart discount" : "Discount";
    },
    discountAmount: {
      get() {
        return this.discount_amount;
      },
      set(value) {
        this.discount_amount = value;
        this.setDiscountAmount(value);
      }
    },
    enableAmount() {
      if (this.discount.type !== "none") {
        return false;
      } else {
        return true;
      }
    },
    discount() {
      if (this.product) {
        return this.product.discount;
      } else {
        return this.order_discount;
      }
    },
    product() {
      return this.cart_products[this.$props.productIndex];
    },
    max() {
      switch (this.discount.type) {
        case "flat":
          if (this.product) {
            return this.$props.productPrice
              .subtract(this.parsePrice(1))
              .toFormat("0.00");
          } else {
            let max = this.parsePrice(this.order_total_price);
            if (this.discount.amount) {
              return max
                .add(this.parsePrice(this.discount.amount))
                .toFormat("0.00");
            } else {
              return 0;
            }
          }
        case "percentage":
          return 99;
        default:
          return 0;
      }
    }
  },

  methods: {
    ...mapMutations("cart", [
      "setCartDiscount",
      "isValidCart",
      "setDiscountError"
    ]),

    runValidation() {
      this.$refs.checkoutObs.validate().then(result => {
        if (this.product) {
          const index = _.findIndex(this.product_map, {
            id: this.product.id
          });
          this.$set(this.product_map[index], "discount_error", !result);
        } else {
          this.setDiscountError(!result);
        }
        this.isValidCart();
      });
    },
    prepareDiscount(value) {
      if (!value) {
        return null;
      } else {
        switch (this.discount.type) {
          case "flat":
            return Number.parseInt(value * 100);
          case "percentage":
            if (value > 0 && value < 100) {
              return Number(value);
            } else {
              return 0;
            }
          case "none":
            return null;
          default:
            return value;
        }
      }
    },
    setDiscountType(value) {
      this.discountAmount = null;

      if (this.product) {
        this.$set(this.product.discount, "type", value);
      } else {
        this.setCartDiscount({ type: value });
      }
    },
    setDiscountAmount(value) {
      value = this.prepareDiscount(value);
      if (this.product) {
        this.$set(this.product.discount, "amount", value);
      } else {
        this.setCartDiscount({ amount: value });
      }
      this.$nextTick(() => {
        this.runValidation();
      });
    }
  }
};
</script>
