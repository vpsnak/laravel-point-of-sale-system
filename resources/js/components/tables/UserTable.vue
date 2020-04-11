<template>
  <data-table v-if="render">
    <template v-slot:item.role_name="{ item }">
      <span v-text="item.role_name" />
    </template>

    <template v-slot:item.active="{ item }">
      <v-checkbox v-model="item.active" :ripple="false" readonly dense />
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom v-if="item.id !== user.id">
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="deauthUser(item.id)"
            class="my-4"
            v-on="on"
            icon
          >
            <v-icon v-text="'mdi-logout-variant'" />
          </v-btn>
        </template>
        <span v-text="`Deauthorize ${item.name}`" />
      </v-tooltip>

      <v-tooltip bottom v-if="item.id !== user.id">
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click="changePasswordDialog(item)"
            class="my-4"
            v-on="on"
            icon
          >
            <v-icon v-text="'mdi-lock-reset'" />
          </v-btn>
        </template>
        <span v-text="`Change ${item.name} password`" />
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
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable({
      icon: "mdi-account-multiple",
      title: "Users",
      model: "users",
      newDialogProps: {
        title: "Add user",
        component: "userForm"
      },
      newBtnTxt: "add user",
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
    ...mapState("datatable", ["data_table"]),
    ...mapState(["user"])
  },
  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("datatable", [
      "setLoading",
      "setDataTable",
      "resetDataTable"
    ]),
    ...mapActions("requests", ["request"]),

    deauthUser(id) {
      this.setLoading(true);
      const payload = {
        method: "get",
        url: `users/${id}/deauth`
      };
      this.request(payload).finally(() => {
        this.setLoading(false);
      });
    },
    changePasswordDialog(item) {
      item.action = "change";
      const payload = {
        show: true,
        width: 600,
        title: `Change ${item.name} password`,
        titleCloseBtn: true,
        icon: "mdi-lock-reset",
        component: "passwordForm",
        component_props: { model: item },
        persistent: true
      };

      this.setDialog(payload);
    },
    view(item) {
      const payload = {
        show: true,
        width: 500,
        title: `View: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "userForm",
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
        component: "userForm",
        component_props: { model: _.cloneDeep(item) },
        persistent: true,
        eventChannel: "data-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
