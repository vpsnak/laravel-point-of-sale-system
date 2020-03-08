<template>
  <v-menu
    v-model="operatorDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title">Created by</h5>
      <v-chip
        pill
        v-on="$props.menu ? on : null"
        color="secondary"
        :small="small"
      >
        <v-icon left>mdi-account-circle</v-icon>
        {{ $props.created_by.name }}
      </v-chip>
    </template>
    <v-card width="450" class="pa-5" outlined shaped>
      <userForm :model="$props.created_by" :readonly="true" />
    </v-card>
  </v-menu>
</template>
<script>
export default {
  props: {
    small: Boolean,
    title: Boolean,
    menu: Boolean,
    created_by: Object
  },

  data() {
    return {
      operatorDetails: false
    };
  }
};
</script>
