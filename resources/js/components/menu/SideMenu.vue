<template>
  <v-navigation-drawer app clipped v-model="menuVisibility" :width="180">
    <v-list dense nav>
      <v-list-item
        v-for="item in side_menu"
        :key="item.id"
        :to="item.action.link"
        :disabled="data_table.loading"
        exact
        link
      >
        <v-list-item-icon>
          <v-icon v-text="item.icon" />
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            <span v-text="item.title" />
          </v-list-item-title>
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
    ...mapMutations("menu", ["setVisibility"])
  }
};
</script>
