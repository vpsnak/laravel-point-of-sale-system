<template>
  <data-table v-if="render">
    <template v-slot:item.email="{ item }">
      <a :href="`mailto:${item.email}`" v-text="item.email" />
    </template>

    <template v-slot:item.phone="{ item }">
      <a :href="`tel:${item.phone}`" v-text="item.phone" />
    </template>

    <template v-slot:item.no_tax="{ item }">
      <span v-text="item.no_tax ? 'Yes' : 'No'" />
    </template>

    <template v-slot:item.house_account_status="{ item }">
      <span v-text="item.house_account_status ? 'Yes' : 'No'" />
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="(item.form = form), editItem(item)"
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
            @click.stop="(item.form = form), viewItem(item)"
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
      newForm: "customerCreateStepper",
      newDialogWidth: 700,
      btnTxt: "New Customer",
      loading: true,
      disableNewBtn: false,
    });

    this.render = true;
  },
  data() {
    return {
      render: false,
      form: "customer",
    };
  },
  computed: {
    ...mapState("datatable", ["data_table"]),
  },
  methods: {
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
    ...mapMutations("datatable", [
      "setLoading",
      "setDataTable",
      "resetDataTable",
    ]),
  },
};
</script>
