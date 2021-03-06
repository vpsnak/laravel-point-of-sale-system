<template>
  <data-table v-if="render">
    <template v-slot:item.active="{ item }">
      <v-checkbox v-model="item.active" :ripple="false" readonly dense />
    </template>
    <template v-slot:item.is_open="{ item }">
      <v-checkbox v-model="item.is_open" :ripple="false" readonly dense />
    </template>
    <template v-slot:item.open_session_user="{ item }">
      <userChip
        v-if="item.open_session_user"
        menu
        :user="item.open_session_user"
      />
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
      icon: "mdi-cash-register",
      title: "Cash Registers",
      model: "cash-registers",
      newDialogProps: {
        title: "Add cash register",
        component: "cashRegisterForm"
      },
      newBtnTxt: "Add cash register",
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

    view(item) {
      const payload = {
        show: true,

        width: 600,
        title: `View: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "cashRegisterForm",
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
        component: "cashRegisterForm",
        component_props: { model: _.cloneDeep(item) },

        persistent: true,
        eventChannel: "data-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
