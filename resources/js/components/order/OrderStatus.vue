<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>mdi-clipboard-list-outline</v-icon>
      <span class="subheading">
        Basic info
      </span>
    </v-card-title>
    <v-container fluid>
      <orderDetails />
    </v-container>
  </v-card>
</template>
<script>
import { mapState } from "vuex";

export default {
  computed: {
    ...mapState("cart", ["order_status", "order_created_by", "order_timestamp"])
  },
  methods: {}
};
</script>
