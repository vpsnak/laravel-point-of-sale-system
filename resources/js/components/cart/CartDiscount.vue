<template>
  <ValidationObserver ref="checkoutObs" tag="v-row">
    <v-col :cols="6">
      <v-select
        @change="validate"
        v-if="editable"
        v-model="discountType"
        :items="discountTypes"
        label="Discount"
        item-text="label"
        item-value="value"
      ></v-select>
      <v-text-field
        v-else-if="!editable"
        :value="discountType"
        label="Discount"
        disabled
      ></v-text-field>
    </v-col>

    <v-col :cols="6" v-if="discountType && discountType !== 'None'">
      <ValidationProvider
        v-slot="{ valid }"
        :rules="`between:${0},${max}`"
        name="Discount amount"
      >
        <v-text-field
          @blur="limits"
          @change="validate"
          @keyup="validate"
          v-model="discountAmount"
          type="number"
          label="Amount"
          :min="0"
          :max="max.toFixed(2)"
          :error-messages="!valid ? 'Invalid amount' : undefined"
          :success="valid"
          :disabled="!editable"
        ></v-text-field>
      </ValidationProvider>
    </v-col>
  </ValidationObserver>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import { mapGetters } from "vuex";

export default {
  props: {
    product_price: Object,
    product_index: Number,
    editable: Boolean
  },

  watch: {
    product_price() {
      this.validate();
    }
  },

  methods: {
    ...mapMutations("cart", [
      "setCartDiscountType",
      "setCartDiscountAmount",
      "isValidDiscount",
      "setDiscountError"
    ]),

    validate() {
      this.setDiscount(this.discountAmount);
      this.isValidDiscount();
    },
    limits() {
      if (
        this.discountType === "Percentage" ||
        this.discountType === "percentage"
      ) {
        if (this.discountAmount > 99) {
          this.discountAmount = "99";
        }
      }

      if (this.discountAmount) {
        let digits = this.discountAmount.split(".");

        if (digits.length === 2 && digits[1].length > 2)
          this.discountAmount = parseFloat(this.discountAmount).toFixed(2);
      }

      this.validate();
    },
    setDiscount(value) {
      if (this.$props.product_index === -1) {
        this.setCartDiscountAmount(value);

        this.$refs.checkoutObs.validate().then(result => {
          this.setDiscountError(!result);
        });
      } else {
        Vue.set(this.product, "discount_amount", value);

        this.$refs.checkoutObs.validate().then(result => {
          Vue.set(this.product, "discount_error", !result);
        });
      }
    }
  },
  computed: {
    ...mapState("cart", [
      "order_total",
      "discountTypes",
      "order_id",
      "cart_products",
      "discount_type",
      "discount_amount",
      "discount_error"
    ]),
    ...mapGetters("price", ["displayPriceNoSign"]),

    product() {
      return this.cart_products[this.$props.product_index];
    },
    max() {
      switch (this.discountType) {
        case "flat":
        case "Flat":
          if (this.$props.product_index === -1) {
            let max = (
              parseFloat(this.order_total) + parseFloat(this.discount_amount)
            ).toFixed(2);

            return parseFloat(max - 0.01);
          } else {
            return this.displayPriceNoSign(this.$props.product_price);
          }
        case "percentage":
        case "Percentage":
          return 99;
        default:
          return 0;
      }
    },
    discountType: {
      get() {
        if (this.$props.product_index === -1) {
          return this.discount_type;
        } else {
          return this.product.discount_type;
        }
      },
      set(value) {
        if (this.$props.product_index === -1) {
          this.setCartDiscountType(value);
        } else {
          Vue.set(this.product, "discount_type", value);
        }

        this.setDiscount(this.discountAmount);
      }
    },
    discountAmount: {
      get() {
        if (this.$props.product_index === -1) {
          return this.discount_amount;
        } else {
          return this.product.discount_amount;
        }
      },
      set(value) {
        this.setDiscount(value);
      }
    }
  }
};
</script>
