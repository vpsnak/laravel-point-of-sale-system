<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider
          rules="required|max:100"
          v-slot="{ errors, valid }"
          name="Name"
        >
          <v-text-field
            v-model="dummyProduct.name"
            label="Name"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider v-slot="{ errors }" name="Notes">
          <v-textarea
            :rows="3"
            v-model="dummyProduct.notes"
            label="Notes"
            :error-messages="errors"
            count
            no-resize
          ></v-textarea>
        </ValidationProvider>
      </v-container>
      <v-container>
        <ValidationProvider
          rules="required|between:0.01,1000"
          v-slot="{ errors, valid }"
          name="Price"
        >
          <v-text-field
            type="number"
            v-model="dummyProduct.final_price"
            label="Price"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <v-row>
          <v-col cols="12" align="center" justify="center">
            <v-btn
              color="primary"
              class="mr-4"
              type="submit"
              :disabled="invalid"
              >Add to cart
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
  data() {
    return {
      dummyProduct: {
        id: null,
        name: "",
        sku: null,
        notes: "",
        price: {
          amount: null
        },
        final_price: null
      }
    };
  },

  methods: {
    submit() {
      this.skuGenerator();
      this.dummyProduct.final_price = this.dummyProduct.price.amount;
      this.$store.commit("cart/addProduct", this.dummyProduct);
      this.$emit("submit", {
        notification: {
          msg: this.dummyProduct.name + " added to cart!",
          type: "success"
        }
      });
    },
    skuGenerator() {
      let random = function() {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
      };
      this.dummyProduct.id = "dummy" + "-" + random();
      this.dummyProduct.sku = "dummy" + "-" + random();
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
