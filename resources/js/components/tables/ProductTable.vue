<template>
  <data-table v-if="render">
    <template v-slot:item.price="{ item }">
      <span v-text="parsePrice(item.price).toFormat()" />
    </template>

    <template v-slot:item.photo_url="{ item }">
      <v-img contain :src="item.photo_url" :width="40" :height="40" />
    </template>

    <template v-slot:item.stock="{ item }">
      <h4 :class="getStockColor(item.stock)" v-text="item.stock" />
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            @click.stop="printProductBarcode(item)"
            :disabled="data_table.loading"
            class="my-4"
            icon
            v-on="on"
          >
            <v-icon v-text="'mdi-barcode'" />
          </v-btn>
        </template>
        <span v-text="'Print Barcode'" />
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="edit(item)"
            class="my-4"
            v-on="on"
            icon
          >
            <v-icon v-text="'edit'" />
          </v-btn>
        </template>
        <span v-text="'Edit'" />
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="view(item)"
            class="my-4"
            v-on="on"
            icon
          >
            <v-icon v-text="'mdi-eye'" />
          </v-btn>
        </template>
        <span v-text="'View'" />
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
      newDialogProps: {
        title: "Add product",
        component: "productForm"
      },
      newBtnTxt: "add product",
      loading: true
    });

    this.render = true;
  },

  data() {
    return {
      render: false
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
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
    },
    view(item) {
      const payload = {
        show: true,
        width: 600,
        title: `View: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "product",
        component_props: { model: item },
        readonly: true,
        eventChannel: ""
      };
      this.setDialog(payload);
    },
    edit(item) {
      const payload = {
        show: true,
        width: 600,
        title: `Edit: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-pencil",
        component: "productForm",
        component_props: { model: _.cloneDeep(item) },
        persistent: true,
        eventChannel: "data-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
