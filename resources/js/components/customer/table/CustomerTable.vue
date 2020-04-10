<template>
  <data-table v-if="render">
    <template v-slot:item.no_tax="{ item }">
      <v-checkbox v-model="item.no_tax" :ripple="false" readonly dense />
    </template>

    <template v-slot:item.house_account_status="{ item }">
      <v-checkbox
        v-model="item.house_account_status"
        :ripple="false"
        readonly
        dense
      />
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click="edit(item)"
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
            @click="view(item)"
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
      icon: "mdi-account-group",
      title: "Customers",
      model: "customers",
      newDialogProps: {
        title: "Add customer",
        component: "customerCreateStepper",
        width: 700,
        no_padding: true
      },
      newBtnTxt: "add customer",
      loading: true
    });

    this.render = true;
  },

  data() {
    return {
      render: false,
      form: "customer"
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
    view(item) {
      const payload = {
        show: true,
        width: 700,
        title: `View: ${item.full_name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "customerForm",
        component_props: { model: item },
        readonly: true,
        eventChannel: ""
      };
      this.setDialog(payload);
    },
    edit(item) {
      const payload = {
        show: true,
        width: 700,
        title: `Edit: ${item.full_name}`,
        titleCloseBtn: true,
        icon: "mdi-pencil",
        component: "customerForm",
        component_props: { model: _.cloneDeep(item) },
        persistent: true,
        eventChannel: "data-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
