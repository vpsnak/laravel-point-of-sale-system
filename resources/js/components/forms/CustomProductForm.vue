<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit"
  >
    <v-container fluid class="overflow-y-auto" style="max-height:60vh;">
      <v-row>
        <v-col :cols="12">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Name"
          >
            <v-text-field
              outlined
              dense
              v-model="customItem.name"
              label="Name"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <ValidationProvider rules="required" v-slot="{ errors }" name="Price">
            <v-text-field
              outlined
              dense
              type="number"
              v-model="price"
              label="Price"
              prefix="$"
              :error-messages="errors"
              style="max-width:100px;"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <v-textarea
            outlined
            dense
            prepend-inner-icon="mdi-message-text-outline"
            :rows="3"
            v-model="customItem.notes"
            label="Notes"
            count
            no-resize
          />
        </v-col>
      </v-row>
    </v-container>

    <v-container>
      <v-row align="center" justify="center">
        <v-btn color="primary" type="submit" :disabled="invalid">
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

      customItem: {
        id: null,
        sku: null,
        type: "custom item",
        name: null,
        notes: null,
        price: {},
        original_price: {},
        discount: {},
        is_discountable: true,
        is_price_editable: true
      }
    };
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
        this.customItem.price = this.customItem.original_price = this.parsePrice(
          Math.round(value * 10000) / 100
        ).toJSON();
      }
    }
  },

  methods: {
    ...mapActions("cart", ["addProduct"]),

    submit() {
      this.customItem.id = this.customItem.sku = `c${parseInt(
        Math.random() * 1000
      )}`;
      this.addProduct(this.customItem);
      this.$emit("submit", true);
    }
  }
};
</script>
