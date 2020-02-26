<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left>mdi-information-variant</v-icon>
      <span class="subheading">
        Info
      </span>
    </v-card-title>
    <v-container>
      <v-row justify="center" align="center">
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <v-menu
            v-model="operatorDetails"
            bottom
            right
            transition="scale-transition"
            :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <h5>Operator</h5>
              <v-chip pill v-on="on" color="secondary">
                <v-icon left>mdi-account-circle</v-icon>
                {{ order_created_by.name }}
              </v-chip>
            </template>
            <v-card width="450" class="pa-5" outlined>
              <userForm :model="order_created_by" :readonly="true" />
            </v-card>
          </v-menu>
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <h5>Created</h5>
          <v-chip pill>
            <v-icon left>mdi-calendar-plus</v-icon>
            {{ order_timestamp.created_at }}
          </v-chip>
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <h5>Last update</h5>
          <v-chip pill>
            <v-icon left>mdi-calendar-edit</v-icon>
            {{ order_timestamp.updated_at }}
          </v-chip>
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <h5>Status</h5>
          <v-chip pill :color="parseStatus(order_status).color">
            <v-icon left>{{ parseStatus(order_status).icon }}</v-icon>
            {{ parseStatus(order_status).name }}
          </v-chip>
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <v-menu
            v-model="methodDetails"
            bottom
            right
            transition="scale-transition"
            :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <h5>Type</h5>
              <v-chip
                pill
                v-on="method === 'retail' ? '' : on"
                :color="parseMethod(method).color"
              >
                <v-icon left>{{ parseMethod(method).icon }}</v-icon>
                {{ parseMethod(method).name }}
              </v-chip>
            </template>
            <v-card class="pa-5" outlined width="600">
              <component
                :is="parseMethod(method).component"
                :model="parseMethod(method).model"
                :readonly="true"
                :hideNotes="true"
              />
            </v-card>
          </v-menu>
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <v-menu
            v-model="customerDetails"
            bottom
            right
            transition="scale-transition"
            :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <h5>Customer</h5>
              <v-chip pill v-on="customer ? on : null" color="primary">
                <v-icon left>mdi-account-outline</v-icon>
                {{ customer ? customer.full_name : "Guest" }}
              </v-chip>
            </template>
            <v-card width="450" class="pa-5" outlined>
              <customerForm :model="customer" :readonly="true" />
            </v-card>
          </v-menu>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
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
    parseMethod(method) {
      switch (method) {
        default:
        case "retail":
          return {
            name: "Cash & Carry",
            component: null,
            icon: "mdi-cart-arrow-right",
            color: "primary",
            model: null
          };
        case "pickup":
          return {
            name: "Store pickup",
            component: "inStorePickupOption",
            icon: "mdi-storefront",
            color: "warning",
            model: "order_delivery_store_pickup"
          };
        case "delivery":
          return {
            name: "Delivery",
            component: "deliveryOption",
            icon: "mdi-truck-delivery",
            color: "purple",
            model: "order_delivery_address"
          };
      }
    },
    parseStatus(status) {
      switch (status) {
        case "canceled":
          return {
            name: _.upperFirst(status.replace("_", " ")),
            icon: "mdi-cancel",
            color: "red"
          };
        case "pending":
          return {
            name: _.upperFirst(status.replace("_", " ")),
            color: "amber--darken-3"
          };
        case "pending_payment":
          return {
            name: _.upperFirst(status.replace("_", " ")),
            icon: "mdi-currency-usd-off",
            color: "amber--darken-3"
          };
        case "paid":
          return {
            name: _.upperFirst(status.replace("_", " ")),
            icon: "mdi-currency-usd",
            color: "cyan"
          };

        case "complete":
          return {
            name: _.upperFirst(status.replace("_", " ")),
            icon: "mdi-cancel",
            color: "green"
          };
        default:
          return {
            name: "huh?",
            icon: "mdi-file-question-outline",
            color: null
          };
      }
    }
  }
};
</script>
