<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit"
  >
    <ValidationProvider
      rules="required|between:0.01,1000"
      v-slot="{ errors }"
      name="Price"
    >
      <v-text-field
        :min="0"
        :max="9999"
        type="number"
        v-model="amount"
        label="Price"
        :error-messages="errors"
      />
    </ValidationProvider>
    <v-row justify="center">
      <v-btn
        color="primary"
        outlined
        type="submit"
        :disabled="invalid"
        v-text="'Add to cart'"
      />
    </v-row>
  </ValidationObserver>
</template>

<script>
import { mapMutations } from "vuex";

export default {
  props: {
    model: Object
  },

  data() {
    return {
      amount: null
    };
  },

  methods: {
    ...mapMutations("cart", ["addProduct"]),

    submit() {
      let giftCard = this.$props.model;

      giftCard.qty = 1;
      giftCard.price = { amount: this.amount };
      giftCard.sku = `giftCard-${giftCard.code}`;

      this.addProduct(giftCard);

      this.$emit("submit", true);
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
