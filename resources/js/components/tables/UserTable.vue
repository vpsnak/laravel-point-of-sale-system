<template>
  <data-table v-if="render">
    <template v-slot:item.role_name="{ item }">
      <span>{{ item.role_name }}</span>
    </template>

    <template v-slot:item.active="{ item }">
      <v-checkbox
        readonly
        v-model="item.active"
        dense
        :ripple="false"
      ></v-checkbox>
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom v-if="item.id !== user.id">
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="deauthUser(item.id)"
            class="my-2"
            v-on="on"
            icon
          >
            <v-icon>mdi-logout-variant</v-icon>
          </v-btn>
        </template>
        <span> Deauthorize {{ item.name }} </span>
      </v-tooltip>

      <v-tooltip bottom v-if="item.id !== user.id">
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click="changePasswordDialog(item)"
            class="my-2"
            v-on="on"
            icon
          >
            <v-icon>mdi-lock-reset</v-icon>
          </v-btn>
        </template>
        <span>Change {{ item.name }} password</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="(item.form = form), editItem(item)"
            class="my-2"
            v-on="on"
            icon
          >
            <v-icon>edit</v-icon>
          </v-btn>
        </template>
        <span>Edit</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            :disabled="data_table.loading"
            @click.stop="(item.form = form), viewItem(item)"
            class="my-2"
            v-on="on"
            icon
          >
            <v-icon>mdi-eye</v-icon>
          </v-btn>
        </template>
        <span>View</span>
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
      newForm: this.form,
      btnTxt: "New User",
      loading: true
    });

    this.render = true;
  },
  data() {
    return {
      render: false,
      form: "userForm"
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"]),
    ...mapState(["user"])
  },
  methods: {
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
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
    }
  }
};
</script>
