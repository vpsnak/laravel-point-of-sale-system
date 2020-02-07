<template>
  <v-stepper v-model="currentCheckoutStep">
    <v-stepper-header>
      <v-stepper-step
        v-for="checkoutStep in checkoutSteps"
        :key="checkoutStep.id"
        :step="checkoutStep.id"
        :complete="checkoutStep.completed"
      >
        {{ checkoutStep.name }}
        <v-divider></v-divider>
      </v-stepper-step>
    </v-stepper-header>
    <v-stepper-items>
      <v-stepper-content
        v-for="checkoutStep in checkoutSteps"
        :key="checkoutStep.id"
        :step="checkoutStep.id"
      >
        <v-col align="center" justify="center">
          <v-icon :color="checkoutStep.color">
            {{ checkoutStep.icon }}
          </v-icon>
          <span class="title">{{ checkoutStep.name }}</span>
        </v-col>

        <component :is="checkoutStep.component"></component>
      </v-stepper-content>
    </v-stepper-items>
  </v-stepper>
</template>

<script>
export default {
  computed: {
    currentCheckoutStep() {
      return this.$store.state.cart.currentCheckoutStep;
    },
    checkoutSteps() {
      return this.$store.state.cart.checkoutSteps;
    }
  }
};
</script>
