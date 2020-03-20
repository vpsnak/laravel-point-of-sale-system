<template>
  <data-table v-if="render">
    <template v-slot:item.price="{ item }">
      {{ parsePrice(item.price).toFormat() }}
    </template>

    <template v-slot:item.photo_url="{ item }">
      <v-img contain :src="item.photo_url" :width="40" :height="40"></v-img>
    </template>

    <template v-slot:item.stock="{ item }">
      <h4 :class="getStockColor(item.stock)">{{ item.stock }}</h4>
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            small
            @click.stop="printProductBarcode(item)"
            :disabled="data_table.loading"
            class="my-2"
            icon
            v-on="on"
          >
            <v-icon>mdi-barcode</v-icon>
          </v-btn>
        </template>
        <span>Print Barcode</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            small
            :disabled="data_table.loading"
            @click.stop="(item.form = form), editItem(item)"
            class="my-2"
            v-on="on"
            icon
          >
            <v-icon small>edit</v-icon>
          </v-btn>
        </template>
        <span>Edit</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            small
            :disabled="data_table.loading"
            @click.stop="(item.form = form), viewItem(item)"
            class="my-1"
            v-on="on"
            icon
          >
            <v-icon small>mdi-eye</v-icon>
          </v-btn>
        </template>
        <span>View</span>
      </v-tooltip>
    </template>
  </data-table>
</template>
<script>
import { mapState, mapGetters, mapMutations } from "vuex";

export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable({
      show: true,
      icon: "mdi-package-variant",
      title: "Products",
      model: "products",
      newForm: this.form,
      btnTxt: "New Product",
      loading: true
    });

    this.render = true;
  },

  data() {
    return {
      render: false,
      form: "productForm"
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"]),
    ...mapGetters(["role"])
  },

  methods: {
    ...mapMutations("dialog", ["editItem", "viewItem"]),
    ...mapMutations("datatable", [
      "setLoading",
      "setDataTable",
      "resetDataTable"
    ]),

    getStockColor(stock) {
      if (stock <= 0) {
        return "red--text";
      } else if (stock <= 10) {
        return "orange--text";
      } else {
        return "";
      }
    },
    printProductBarcode(item) {
      window.open(`/product_barcode/${item.id}`, "_blank");
    }
  }
};
</script>
