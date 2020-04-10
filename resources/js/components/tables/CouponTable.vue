<template>
  <data-table v-if="render">
    <template v-slot:item.discount.amount="{ item }">
      <span v-text="couponAmount(item)" />
    </template>
    <template v-slot:item.actions="{ item }">
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
import { mapState, mapMutations } from "vuex";
export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable({
      icon: "mdi-ticket-percent",
      title: "Coupons",
      model: "coupons",
      newDialogProps: {
        title: "New coupon",
        component: "couponForm"
      },
      newBtnTxt: "new coupon",
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
    ...mapMutations("datatable", ["setDataTable", "resetDataTable"]),

    couponAmount(item) {
      if (item.discount.type === "flat") {
        return this.parsePrice(item.discount.amount).toFormat();
      } else {
        return `${item.discount.amount} %`;
      }
    },
    view(item) {
      const payload = {
        show: true,
        width: 500,
        title: `View: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "couponForm",
        component_props: { model: item },
        readonly: true,
        eventChannel: ""
      };
      this.setDialog(payload);
    },
    edit(item) {
      const payload = {
        show: true,
        width: 500,
        title: `Edit: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-pencil",
        component: "couponForm",
        component_props: { model: _.cloneDeep(item) },
        persistent: true,
        eventChannel: "data-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
