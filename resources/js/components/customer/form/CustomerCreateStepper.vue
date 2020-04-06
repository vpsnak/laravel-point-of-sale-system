<template>
  <v-stepper v-model="step" vertical>
    <v-stepper-step :complete="step > 1" step="1">
      <div class="d-flex align-center">
        <v-icon v-text="'mdi-card-account-details'" class="mr-2" />
        <h5 v-text="'Basic info'" />
      </div>
    </v-stepper-step>
    <v-stepper-step :complete="step > 2" step="2">
      <div class="d-flex align-center">
        <v-icon v-text="'mdi-map-marker'" class="mr-2" />
        <h5 v-text="'Address'" />
      </div>
    </v-stepper-step>

    <v-stepper-content step="1">
      <customerForm />
    </v-stepper-content>
    <v-stepper-content step="2">
      <addressForm />
    </v-stepper-content>
    <!-- for debug purposes only. delete lines below -->
    <v-btn @click="step--" v-text="'prev'" />
    <v-btn @click="step++" v-text="'next'" />
  </v-stepper>
</template>

<script>
export default {
  data() {
    return {
      step: 1,
    };
  },
};
</script>
