<template>
  <ValidationObserver v-slot="{ invalid }" tag="form" @submit.prevent="submit">
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
      <v-row>
        <v-col :cols="12">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Name"
          >
            <v-text-field
              disabled
              v-model="giftCard.name"
              label="Name"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <ValidationProvider
            rules="required|between:0.01,1000"
            v-slot="{ errors }"
            name="Price"
          >
            <v-text-field
              type="number"
              v-model="price"
              label="Price"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Code"
          >
            <v-text-field
              disabled
              v-model="giftCard.code"
              label="Code"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <v-textarea
            outlined
            dense
            prepend-inner-icon="mdi-message-text-outline"
            :rows="3"
            v-model="giftCard.notes"
            label="Notes"
            count
            no-resize
          ></v-textarea>
        </v-col>
        <v-col :cols="12">
          <v-alert v-if="giftCardEnabled" dense outlined type="warning">
            This gift card with {{ parsePrice(giftCard.price).toFormat() }} is
            enabled. If you want to recharge it type the amount and add it to
            cart
          </v-alert>
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <v-row justify="center">
        <v-btn color="primary" class="mr-4" type="submit" :disabled="invalid">
          Add to cart
        </v-btn>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
  data() {
    return {
      price_amount: null,
      giftCardEnabled: false,
      giftCard: {
        id: null,
        sku: null,
        type: "giftcard",
        notes: null,
        price: {}
      }
    };
  },

  mounted() {
    this.$root.$on("barcodeScan", code => {
      this.getGiftCard(code);
    });

    this.giftCard.id = this.giftCard.sku = `g${parseInt(Math.random() * 1000)}`;
  },

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    price: {
      get() {
        return this.price_amount;
      },
      set(value) {
        this.price_amount = value;
        this.giftCard.price = this.parsePrice(
          Math.round(value * 10000) / 100
        ).toJSON();
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),
    ...mapActions("cart", ["addProduct"]),

    getGiftCard(code) {
      const payload = {
        method: "post",
        url: "giftcards/search",
        data: { keyword: code }
      };
      this.request(payload).then(response => {
        if (response.data[0]) {
          this.giftCard = { ...this.giftCard, ...response.data[0] };
          this.price_amount = this.parsePrice(this.giftCard.price).toFormat(
            "0.00"
          );
        }
      });
    },
    submit() {
      this.addProduct(this.giftCard);
      this.$emit("submit", true);
    }
  }
};
</script>
