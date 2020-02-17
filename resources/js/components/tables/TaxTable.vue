<template>
  <div>
    <data-table v-if="render">
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
  </div>
</template>

<script>
import { mapMutations, mapState } from "vuex";

export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable({
      icon: "mdi-sack-percent",
      title: "Taxes",
      model: "taxes",
      newForm: this.form,
      btnTxt: "New Tax",
      loading: true,
      eventChannel: "data-table"
    });

    this.render = true;
  },

  data() {
    return {
      render: false,
      form: "taxForm"
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapMutations("dialog", ["editItem", "viewItem"]),
    ...mapMutations("datatable", ["setDataTable", "resetDataTable"])
  }
};
</script>
