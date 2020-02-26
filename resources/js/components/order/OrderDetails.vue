<template>
  <v-row>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-account-circle</v-icon>
          <span class="subtitle-1">
            Operator&nbsp;
          </span>
          <v-menu
            v-model="operatorDetails"
            bottom
            right
            transition="scale-transition"
            :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <v-chip pill v-on="on" color="primary">
                {{ order_created_by.name }}
              </v-chip>
            </template>
            <v-card width="450" class="pa-5" outlined>
              <userForm :model="order_created_by" :readonly="true" />
            </v-card>
          </v-menu>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-calendar-plus</v-icon>
          <span class="subtitle-1">
            Created:
            <b class="primary--text">
              <i>{{ order_timestamp.created_at }}</i>
            </b>
          </span>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-calendar-edit</v-icon>
          <span class="subtitle-1">
            Updated:
            <b class="primary--text">
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
          <v-icon left>mdi-format-lists-checked</v-icon>
          <span class="subtitle-1">
            Type&nbsp;
          </span>
          <v-menu
            v-model="methodDetails"
            bottom
            right
            transition="scale-transition"
            :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <v-chip
                pill
                v-on="method === 'retail' ? '' : on"
                :color="parseMethodColor(method)"
              >
                {{ parseMethod(method) }}
              </v-chip>
            </template>
            <v-card class="pa-5" outlined width="600">
              <component
                :is="methodComponent(method)"
                :model="methodModel(method)"
                :readonly="true"
                :hideNotes="true"
              />
            </v-card>
          </v-menu>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col :lg="4" :md="6" :cols="12">
      <v-card elevation="0">
        <v-card-title>
          <v-icon left>mdi-account-outline</v-icon>
          <span class="subtitle-1">
            Customer&nbsp;
          </span>
          <v-menu
            v-model="customerDetails"
            bottom
            right
            transition="scale-transition"
            :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <v-chip pill v-on="customer ? on : null" color="primary">
                {{ customer ? customer.full_name : "Guest" }}
              </v-chip>
            </template>
            <v-card width="450" class="pa-5" outlined>
              <customerForm :model="customer" :readonly="true" />
            </v-card>
          </v-menu>
        </v-card-title>
      </v-card>
    </v-col>
  </v-row>
</template>
<script>
import { mapState } from "vuex";

export default {
  data() {
    return {
      operatorDetails: false,
      methodDetails: false,
      customerDetails: false
    };
  },
  computed: {
    ...mapState("cart", [
      "order_created_by",
      "order_timestamp",
      "order_status",
      "order_delivery_store_pickup",
      "order_delivery_address",
      "method",
      "customer"
    ])
  },

  methods: {
    methodComponent(method) {
      switch (method) {
        default:
        case "retail":
          return;
        case "pickup":
          return "inStorePickupOption";
        case "delivery":
          return "deliveryOption";
      }
    },
    methodModel(method) {
      switch (method) {
        default:
        case "retail":
          return;
        case "pickup":
          return "order_delivery_store_pickup";
        case "delivery":
          return "order_delivery_address";
      }
    },
    parseMethodColor(method) {
      switch (method) {
        case "retail":
          return "primary";
        case "pickup":
          return "warning";
        case "delivery":
          return "purple";
      }
    },
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
