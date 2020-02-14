<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>mdi-clipboard-list-outline</v-icon>
      <span class="subheading">
        Status
      </span>
    </v-card-title>
    <v-container>
      <v-row align="center" dense>
        <v-col :cols="6">
          <span class="subheading">
            Order:
            <b :class="statusColor(order_status)">
              {{ parseStatusName(order_status) }}
            </b>
          </span>
        </v-col>
        <v-col :cols="6">
          <span class="subheading">
            MAS:
            <b :class="statusColor(order_status)">
              {{ parseStatusName(order_status) }}
            </b>
          </span>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>
<script>
import { mapState } from "vuex";

export default {
  computed: {
    ...mapState("cart", ["order_status"])
  },
  methods: {
    parseStatusName(value) {
      return _.upperFirst(value.replace("_", " "));
    },
    statusColor(status) {
      switch (status) {
        case "canceled":
          return "red--text";
        case "pending":
          return "primary--text";
        case "pending_payment":
          return "primary--text";
        case "paid":
          return "cyan--text";
        case "complete":
          return "green--text";
        default:
          return "";
      }
    }
  }
};
</script>
