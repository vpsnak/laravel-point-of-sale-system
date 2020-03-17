<template>
  <data-table v-if="render">
    <template v-slot:item.discount.amount="{ item }">
      {{ couponAmount(item) }}
    </template>
    <template v-slot:item.actions="{ item }">
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
            class="my-2"
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
import { mapState, mapMutations } from "vuex";
export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable({
      icon: "mdi-ticket-percent",
      title: "Coupons",
      model: "coupons",
      newForm: this.form,
      btnTxt: "New Coupon",
      loading: true
    });

    this.render = true;
  },
  data() {
    return {
      render: false,
      form: "couponForm"
    };
  },
  computed: {
    ...mapState("datatable", ["data_table"])
  },
  methods: {
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
    ...mapMutations("datatable", ["setDataTable", "resetDataTable"]),

    couponAmount(item) {
      if (item.discount.type === "flat") {
        return this.parsePrice(item.discount.amount).toFormat();
      } else {
        return `${item.discount.amount}%`;
      }
    }
  }
};
</script>
