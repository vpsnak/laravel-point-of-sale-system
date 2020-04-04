<template>
  <v-stepper v-model="step" vertical>
    <v-stepper-step :complete="step > 1" step="1">
      <h5 v-text="'Basic info'" />
    </v-stepper-step>
    <v-stepper-step :complete="step > 2" step="2">
      <h5 v-text="'Address'" />
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
