<template>
  <data-table v-if="data_table.model">
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
import { mapMutations, mapActions, mapState } from "vuex";

export default {
  mounted() {
    this.setDataTable({
      show: true,
      icon: "mdi-sack-percent",
      title: "Taxes",
      headers: this.headers,
      model: "taxes",
      component: "taxForm",
      loading: true
    });
  },

  data() {
    return {
      form: "taxForm",
      headers: [
        { text: "#", value: "id" },
        { text: "Name", value: "name" },
        { text: "Percentage", value: "percentage" },
        { text: "Actions", value: "actions" }
      ]
    };
  },
  computed: {
    ...mapState("datatable", ["data_table"])
  },
  methods: {
    ...mapMutations("dialog", ["editItem", "viewItem"]),
    ...mapMutations("datatable", ["setLoading", "setDataTable"]),
    ...mapActions(["getOne", "delete"])
  }
};
</script>
