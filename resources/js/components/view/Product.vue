<template>
  <v-container v-if="productData">
    <v-row>
      <v-col cols="12" md="6">
        <v-card :img="productData.photo_url" height="100%"></v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>{{ productData.name }}</v-card-title>
          <v-card-text>
            <div class="subtitle-1">Sku: {{ productData.sku }}</div>
            <div class="subtitle-1">
              Price: {{ parseFloat(product.final_price).toFixed(2) }} $
            </div>
            <div class="subtitle-1">Stock: {{ productData.stock }}</div>
            <div class="subtitle-1">
              Created at: {{ productData.created_at }}
            </div>
            <div class="subtitle-1" v-if="productData.barcode">
              <barcode :value="productData.barcode" format="CODE39"
                >The rendering fails.</barcode
              >
            </div>
            <div class="subtitle-1" v-else-if="productData.sku">
              <barcode :value="productData.sku" format="CODE39"
                >The rendering fails.</barcode
              >
            </div>
          </v-card-text>
          <v-simple-table fixed-header>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">Store name</th>
                  <th class="text-left">Qty</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="store in productData.stores" :key="store.id">
                  <td>{{ store.name }}</td>
                  <td>{{ store.pivot.qty }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card>
      </v-col>
    </v-row>
    <v-container v-if="productData.description">
      <v-row>
        <v-col :cols="12">
          <h3 class="subtitle-1">Description</h3>
        </v-col>
        <v-col :cols="12">
          <div v-html="productData.description"></div>
        </v-col>
      </v-row>
    </v-container>
  </v-container>
  <v-container v-else>
    <v-row>
      <v-col cols="12" align="center" justify="center">
        <v-progress-circular
          indeterminate
          color="secondary"
        ></v-progress-circular>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Object
  },
  data() {
    return {
      headers: [
        { text: "Store name", value: "store.name" },
        { text: "Qty", value: "store.pivot.qty" }
      ],
      product: null
    };
  },
  mounted() {
    if (this.model)
      this.getOne({
        model: "products",
        data: {
          id: this.model.id
        }
      }).then(result => {
        this.product = result;
      });
  },
  computed: {
    productData() {
      return this.product;
    }
  },
  methods: {
    ...mapActions({
      getOne: "getOne"
    })
  }
};
</script>
