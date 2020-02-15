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
          <v-icon left>mdi-account-outline</v-icon>
          <span class="subtitle-1">
            Status
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-package-variant</v-icon>
          <span class="subtitle-1">
            Items
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :cols="4">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-account-outline</v-icon>
          <span class="subtitle-1">
            Customer
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
    ...mapState("cart", ["order", "order_created_by", "order_timestamp"])
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
