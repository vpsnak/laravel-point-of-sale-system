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
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            small
            :disabled="data_table.loading"
            @click="changePasswordDialog(item)"
            class="my-2"
            v-on="on"
            icon
          >
            <v-icon small>mdi-lock-reset</v-icon>
          </v-btn>
        </template>
        <span>Change {{ item.name }} password</span>
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
import { mapMutations, mapState } from "vuex";

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
    ...mapState("datatable", ["data_table"])
  },
  methods: {
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
    ...mapMutations("datatable", [
      "setLoading",
      "setDataTable",
      "resetDataTable"
    ]),

    changePasswordDialog(item) {
      item.action = "change";
      const payload = {
        show: true,
        width: 600,
        title: `Change password for user: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-lock-reset",
        component: "passwordForm",
        model: item,
        persistent: true
      };

      this.setDialog(payload);
    }
  }
};
</script>
