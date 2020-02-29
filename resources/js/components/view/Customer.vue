<template>
  <v-container>
    <v-row v-if="customerData">
      <v-col cols="12">
        <v-card>
          <v-toolbar dense color="blue-grey darken-4" dark>
            <v-toolbar-title>Personal Information</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <customerForm :model="customerData" @submit="submit"></customerForm>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" v-if="customerData.addresses">
        <v-card>
          <v-toolbar dense color="blue-grey darken-4" dark>
            <v-toolbar-title>Address Book</v-toolbar-title>
          </v-toolbar>
          <v-tabs>
            <v-tab v-for="address in customerData.addresses" :key="address.id">
              {{ address.first_name }} {{ address.last_name }}
              <span v-if="address.is_default_billing">(Default Billing)</span>
              <span v-if="address.is_default_shipping">(Default Shipping)</span>
            </v-tab>
            <v-tab-item
              v-for="address in customerData.addresses"
              :key="address.id"
            >
              <v-card>
                <v-card-text>
                  <addressForm @submit="submit" :model="address"></addressForm>
                </v-card-text>
              </v-card>
            </v-tab-item>
          </v-tabs>
        </v-card>
      </v-col>
    </v-row>
    <v-row v-else>
      <v-col cols="12" align="center" justify="center">
        <v-progress-circular
          indeterminate
          color="secondary"
        ></v-progress-circular>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Object
  },
  data() {
    return {
      customer: null
    };
  },
  mounted() {
    if (this.$props.model)
      this.request({
        method: "get",
        url: `customers/get/${this.$props.model.id}`
      }).then(result => {
        this.customer = result;
      });
  },
  computed: {
    customerData() {
      return this.customer;
    }
  },
  methods: {
    submit() {
      this.$emit("submit", {
        action: "paginate",
        notification: {
          msg: "Customer updated successfully",
          type: "success"
        }
      });
    },
    ...mapActions("requests", ["request"])
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
