<template>
  <v-menu
    v-model="storeDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title">Store</h5>
      <v-chip pill v-on="$props.menu ? on : false">
        <v-icon left>mdi-store</v-icon>
        {{ $props.store.name }}
      </v-chip>
    </template>
    <v-card class="pa-5" width="450" outlined shaped>
      <storeForm :model="$props.store" :readonly="true" />
    </v-card>
  </v-menu>
</template>

<script>
export default {
  props: {
    title: Boolean,
    menu: Boolean,
    store: Object
  },

  data() {
    return {
      storeDetails: false
    };
  }
};
</script>
