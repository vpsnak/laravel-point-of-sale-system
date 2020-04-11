<template>
  <v-stepper v-model="step">
    <v-stepper-header>
      <v-stepper-step :complete="step > 1" :step="1">
        <div class="d-flex align-center">
          <v-icon v-text="'mdi-card-account-details'" class="mx-2" />
          <h5 v-text="'Basic info'" />
        </div>
      </v-stepper-step>

      <v-divider />

      <v-stepper-step :complete="step > 2" :step="2">
        <div class="d-flex align-center">
          <v-icon v-text="'mdi-map-marker'" class="mx-2" />
          <h5 v-text="'Address'" />
        </div>
      </v-stepper-step>

      <v-divider />

      <v-stepper-step :complete="step > 3" :step="3">
        <div class="d-flex align-center">
          <v-icon v-text="'mdi-eye'" class="mx-2" />
          <h5 v-text="'View'" />
        </div>
      </v-stepper-step>
    </v-stepper-header>

    <v-stepper-content :step="1">
      <customerForm :key="customer_id" :model="customer" stepper />
    </v-stepper-content>
    <v-stepper-content :step="2">
      <addressForm :key="customer_id" :customer="customer" stepper />
    </v-stepper-content>
    <v-stepper-content :step="3">
      <customerView :key="customer_id" :customer="customer" />
    </v-stepper-content>
  </v-stepper>
</template>

<script>
import { EventBus } from "../../../plugins/eventBus";
export default {
  props: {
    addToCart: Boolean
  },

  data() {
    return {
      step: 1,
      customer: {},
      customer_id: null
    };
  },

  mounted() {
    EventBus.$on("customer-form-submit", customer => {
      this.customer = customer;
      this.customer_id = customer.id;
      this.step = 2;

      if (this.$props.addToCart) {
        EventBus.$emit("customer-search", customer);
      }
    });
    EventBus.$on("customer-form-back", () => {
      this.step = 1;
    });
  },

  beforeDestroy() {
    EventBus.$off("customer-form-submit");
    EventBus.$off("customer-form-back");
  }
};
</script>
