<template>
  <ValidationObserver ref="checkoutObs" tag="v-row">
    <v-col :cols="6">
      <v-select
        @change="setDiscountType($event)"
        v-if="$props.editable"
        :value="discount.type"
        item-text="text"
        item-value="value"
        :items="discountTypes"
        label="Discount"
      ></v-select>
      <v-text-field
        v-else
        :value="discount.type"
        label="Discount"
        disabled
      ></v-text-field>
    </v-col>

    <v-col :cols="6" v-show="showAmount">
      <ValidationProvider
        v-slot="{ valid }"
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
          :error-messages="!valid ? 'Invalid amount' : undefined"
          :success="valid"
        ></v-text-field>
      </ValidationProvider>
      <v-text-field
        v-else
        :value="discountAmount"
        label="Amount"
        :disabled="true"
      ></v-text-field>
    </v-col>
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
    console.log(this.discount.amount);
    if (this.discount.amount) {
      this.discountAmount = Number(this.discount.amount / 100);
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
      "productMap",
      "order_total_price",
      "discountTypes",
      "order_id",
      "cart_products",
      "order_discount",
      "discount_error"
    ]),

    discountAmount: {
      get() {
        return this.discount_amount;
      },
      set(value) {
        this.discount_amount = value;
        this.setDiscountAmount(value);
      }
    },
    showAmount() {
      if (this.discount.type !== "none") {
        return true;
      } else {
        return false;
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
            return this.$props.productPrice.toFormat("0.00");
          } else {
            let max = this.order_total_price;
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
      "isValidDiscount",
      "setDiscountError"
    ]),

    runValidation() {
      this.$refs.checkoutObs.validate().then(result => {
        if (this.product) {
          const index = _.findIndex(this.productMap, {
            id: this.product.id
          });
          this.productMap[index].discount_error = !result;
        } else {
          this.setDiscountError(!result);
        }
        this.isValidDiscount();
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
            return Number(value);
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
