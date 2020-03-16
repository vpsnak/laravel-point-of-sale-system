<template>
  <v-menu
    v-model="storeDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
    :small="small"
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title">Store</h5>
      <v-chip :small="small" pill v-on="$props.menu ? on : false">
        <v-icon left>mdi-store</v-icon>
        <b>{{ $props.store.name }}</b>
      </v-chip>
    </template>
    <v-card class="pa-5" width="450" outlined shaped>
      <storeForm :model="$props.store" :readonly="true" />
    </v-card>
  </v-menu>
</template>

<script>
import { EventBus } from "../../../plugins/eventBus";
export default {
  props: {
    small: Boolean,
    title: Boolean,
    menu: Boolean,
    store: Object
  },

  beforeDestroy() {
    EventBus.$off("overlay");
  },

  data() {
    return {
      storeDetails: false
    };
  },

  watch: {
    storeDetails(value) {
      EventBus.$emit("overlay", value);
    }
  }
};
</script>
