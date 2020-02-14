<template>
  <ValidationObserver
    v-slot="{ invalid }"
    ref="giftCardToCartObs"
    tag="form"
    @submit.prevent="submit"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
      <ValidationProvider
        rules="required|max:191"
        v-slot="{ errors, valid }"
        name="Name"
      >
        <v-text-field
          disabled
          v-model="giftCard.name"
          label="Name"
          :error-messages="errors"
          :success="valid"
        ></v-text-field>
      </ValidationProvider>
      <ValidationProvider
        :rules="{
          required: true,
          max_value: 99999,
          min_value: 1,
          regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g
        }"
        v-slot="{ errors, valid }"
        name="Price Amount"
      >
        <v-text-field
          type="number"
          v-model="giftCard.final_price"
          label="Price"
          :error-messages="errors"
          :success="valid"
        ></v-text-field>
      </ValidationProvider>

      <ValidationProvider
        rules="required|max:191"
        v-slot="{ errors, valid }"
        name="Code"
      >
        <v-text-field
          disabled
          v-model="giftCard.code"
          label="Code"
          :error-messages="errors"
          :success="valid"
        ></v-text-field>
      </ValidationProvider>

      <ValidationProvider rules="max:191" v-slot="{ errors }" name="Notes">
        <v-textarea
          :rows="3"
          v-model="giftCard.notes"
          label="Notes"
          :error-messages="errors"
          count
          no-resize
        ></v-textarea>
      </ValidationProvider>
      <v-alert v-if="giftCardEnabled" dense outlined type="warning">
        This gift card with {{ giftCardEnabledAmount }} $ is enabled. If you
        want to recharge it type the amount and add it to cart
      </v-alert>
    </v-container>
    <v-container>
      <v-row>
        <v-col cols="12" align="center" justify="center">
          <v-btn
            color="secondary"
            class="mr-4"
            type="submit"
            :disabled="invalid"
            >Add to cart</v-btn
          >
        </v-col>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
export default {
  data() {
    return {
      giftCardEnabledAmount: null,
      giftCardEnabled: false,
      giftCard: {
        id: null,
        name: "",
        code: "",
        sku: null,
        notes: "",
        price: {
          amount: null
        },
        final_price: null
      }
    };
  },
  mounted() {
    this.$root.$on("barcodeScan", code => {
      this.getGiftCard(code);
    });
  },
  beforeDestroy() {
    this.$root.$off("barcodeScan");
  },
  methods: {
    getGiftCard(code) {
      let payload = {
        model: "gift-cards",
        keyword: code
      };
      this.$store.dispatch("search", payload).then(response => {
        if (response[0]) {
          this.giftCard.name = response[0].name;
          this.giftCard.code = response[0].code;
          this.giftCard.final_price = response[0].amount;
          if (response[0].enabled) {
            this.giftCardEnabled = true;
            this.giftCard.final_price = 0;
            this.giftCardEnabledAmount = response[0].amount;
          }
        }
      });
    },
    submit() {
      this.IdAndSkuGenerator();
      this.giftCard.final_price = this.giftCard.price.amount;
      this.giftCard.name = "Gift card - " + this.giftCard.name;
      this.$store.commit("cart/addProduct", this.giftCard);
      this.$emit("submit", {
        notification: {
          msg: this.giftCard.name + " added to cart!",
          type: "success"
        }
      });
    },
    IdAndSkuGenerator() {
      let random = function() {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
      };
      this.giftCard.id = "giftCard" + "-" + random();
      this.giftCard.sku = "giftCard" + "-" + this.giftCard.code;
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
