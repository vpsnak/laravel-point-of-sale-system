<template>
  <data-table v-if="render">
    <template v-slot:item.is_enabled="{ item }">
      <v-checkbox v-model="item.is_enabled" dense />
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
            @click.stop="view(item)"
            :disabled="data_table.loading"
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
import { mapMutations, mapState } from "vuex";

export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable({
      show: true,
      icon: "mdi-inbox-multiple",
      title: "Categories",
      model: "categories",
      newForm: this.form,
      btnTxt: "New Category",
      loading: true
    });

    this.render = true;
  },
  data() {
    return {
      render: false,
      form: "categoryForm"
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
        fullscreen: false,
        width: 600,
        title: `View: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "categoryForm",
        component_props: { model: item },
        readonly: true,
        persistent: false,
        eventChannel: "",
        no_padding: false
      };
      this.setDialog(payload);
    },
    edit(item) {
      const payload = {
        show: true,
        fullscreen: false,
        width: 600,
        title: `Edit: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-pencil",
        component: "categoryForm",
        component_props: { model: _.cloneDeep(item) },
        readonly: false,
        persistent: true,
        eventChannel: "data-table",
        no_padding: false
      };
      this.setDialog(payload);
    }
  }
};
</script>
