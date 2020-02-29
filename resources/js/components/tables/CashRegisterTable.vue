<template>
  <data-table v-if="render">
    <template v-slot:item.active="{ item }">{{ item.active ? "Yes" : "No" }}</template>
    <template v-slot:item.is_open="{ item }">{{ item.is_open ? "Yes" : "No" }}</template>
    <template
      v-slot:open_session_user.name="{ item }"
    >{{ item.open_session_user ? item.open_session_user.name : "" }}</template>

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
      icon: "mdi-cash-register",
      title: "Cash Registers",
      model: "cash-registers",
      newForm: this.form,
      btnTxt: "New Cash Register",
      loading: true
    });

    this.render = true;
  },
  data() {
    return {
      render: false,
      form: "cashRegisterForm"
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
    ...mapMutations("datatable", ["setDataTable", "resetDataTable"])
  }
};
</script>
