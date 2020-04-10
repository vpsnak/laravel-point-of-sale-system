<template>
  <v-menu
    v-model="operatorDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
    offset-y
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title" v-text="'Created by'" />
      <v-chip
        label
        v-on="$props.menu ? on : null"
        color="primary"
        :small="small"
      >
        <v-icon left v-text="'mdi-account-circle'" />
        <b v-text="$props.user.name" />
      </v-chip>
    </template>
    <v-card width="450" class="pa-5" outlined>
      <userForm :model="$props.user" readonly />
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
    user: Object
  },

  beforeDestroy() {
    EventBus.$off("overlay");
  },

  data() {
    return {
      operatorDetails: false
    };
  },

  watch: {
    operatorDetails(value) {
      EventBus.$emit("overlay", value);
    }
  }
};
</script>
