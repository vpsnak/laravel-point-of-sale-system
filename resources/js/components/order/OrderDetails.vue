<template>
  <v-row justify="center" align="center" no-gutters>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-account-circle</v-icon>
          <span class="subtitle-1">
            Operator:
            <b>
              <i>{{ order_created_by.name }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-calendar-plus</v-icon>
          <span class="subtitle-1">
            Created at:
            <b>
              <i>{{ order_timestamp.created_at }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-calendar-check-outline</v-icon>
          <span class="subtitle-1">
            Updated at:
            <b>
              <i>{{ order_timestamp.updated_at }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-file-tree</v-icon>
          <span class="subtitle-1">
            Status:
            <b :class="statusColor(order_status)">
              <i>{{ parseStatusName(order_status) }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-package-variant</v-icon>
          <span class="subtitle-1">
            Type:
            <b>
              <i>{{ parseMethodName(method) }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-account-outline</v-icon>
          <span class="subtitle-1">
            Customer:
            <b>
              <i>{{ customer ? customer.name : "Guest" }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
  </v-row>
</template>
<script>
import { mapState } from "vuex";

export default {
  computed: {
    ...mapState("cart", [
      "order_created_by",
      "order_timestamp",
      "order_status",
      "method",
      "customer"
    ])
  },
  methods: {
    parseMethodName(method) {
      switch (method) {
        case "retail":
        case "delivery":
          return _.startCase(method);
        case "pickup":
          return "Store pickup";
      }
    },
    parseStatusName(status) {
      return _.upperFirst(status.replace("_", " "));
    },
    statusColor(status) {
      switch (status) {
        case "canceled":
          return "red--text";
        case "pending":
          return "amber--text";
        case "pending_payment":
          return "amber--text";
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
