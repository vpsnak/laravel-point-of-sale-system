<template>
  <v-row justify="center" align="center" no-gutters>
    <v-col :lg="4" :md="6" :cols="12">
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
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-calendar-plus</v-icon>
          <span class="subtitle-1">
            Created:
            <b>
              <i>{{ order_timestamp.created_at }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-calendar-check-outline</v-icon>
          <span class="subtitle-1">
            Updated:
            <b>
              <i>{{ order_timestamp.updated_at }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-file-tree</v-icon>
          <span class="subtitle-1">
            Status:
            <b :class="statusColor(order_status)">
              <i>{{ parseStatus(order_status) }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left></v-icon>
          <span class="subtitle-1">
            Type:
            <b>
              <i>{{ parseMethod(method) }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-account-outline</v-icon>
          <span class="subtitle-1">
            Customer:
            <b>
              <i>{{ customer ? customer.full_name : "Guest" }}</i>
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
    parseMethod(method) {
      switch (method) {
        case "retail":
          return "Cash & Carry";
        case "pickup":
          return "Store pickup";
        case "delivery":
          return "Delivery";
      }
    },
    parseStatus(status) {
      if (status) {
        return _.upperFirst(status.replace("_", " "));
      }
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
