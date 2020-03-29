<template>
  <v-navigation-drawer app clipped v-model="menuVisibility" :width="180">
    <v-list dense>
      <v-list-item
        v-for="menu_item in side_menu"
        :key="menu_item.id"
        :to="menuAction(menu_item)"
        :disabled="data_table.loading"
      >
        <v-list-item-icon>
          <v-icon v-text="menu_item.icon" />
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title v-text="menu_item.title" />
        </v-list-item-content>
      </v-list-item>
      <v-divider class="my-4" />
      <v-subheader class="mt-5 grey--text text--darken-1">
        "Designed" and "developed" by WebO2
      </v-subheader>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  computed: {
    ...mapState("menu", ["visibility", "side_menu"]),
    ...mapState("datatable", ["data_table"]),

    menuVisibility: {
      get() {
        return this.visibility;
      },
      set(value) {
        this.setVisibility(value);
      }
    }
  },
  methods: {
    ...mapMutations("menu", ["setVisibility"]),

    menuAction(item) {
      if (_.has(item, "action.link")) {
        return `/${item.action.link}`;
      } else if (_.has(item, "action.method")) {
        return item.action.method;
      }
    }
  }
};
</script>
