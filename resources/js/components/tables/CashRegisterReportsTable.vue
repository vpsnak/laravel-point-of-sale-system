<template>
  <data-table v-if="render">
    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="view(item)"
            class="my-2"
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
      icon: "mdi-file-document-box",
      title: "Cash Register Reports",
      model: "cash-register-reports",
      disableNewBtn: true,
      loading: true
    });

    this.render = true;
  },
  data() {
    return {
      render: false,
      form: "cashRegisterReports"
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("datatable", ["setDataTable", "resetDataTable"]),
    ...mapMutations("cart", ["setCheckoutDialog"]),

    view(item) {
      const payload = {
        show: true,

        width: 600,
        title: `View: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "cashRegisterReports",
        component_props: { model: item },
        readonly: true,

        eventChannel: ""
      };
      this.setDialog(payload);
    }
  }
};
</script>
