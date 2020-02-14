<template>
  <v-card>
    <v-card-text>
      <v-stepper v-model="currentCheckoutStep">
        <v-stepper-header>
          <v-stepper-step
            v-for="checkoutStep in checkoutSteps"
            :key="checkoutStep.id"
            :step="checkoutStep.id"
            :complete="checkoutStep.completed"
          >
            {{ checkoutStep.name }}
            <v-divider />
          </v-stepper-step>
        </v-stepper-header>
        <v-stepper-items>
          <v-stepper-content
            v-for="checkoutStep in checkoutSteps"
            :key="checkoutStep.id"
            :step="checkoutStep.id"
          >
            <v-col align="center">
              <v-icon :color="checkoutStep.color">
                {{ checkoutStep.icon }}
              </v-icon>
              <span class="title">{{ checkoutStep.name }}</span>
            </v-col>

            <component :is="checkoutStep.component" />
          </v-stepper-content>
        </v-stepper-items>
      </v-stepper>
    </v-card-text>
    <v-card-actions>
      <v-container>
        <v-row no-gutters align="center" justify="center">
          <v-spacer></v-spacer>

          <v-btn
            @click="previousStep()"
            v-show="!order_id && currentCheckoutStep === 2"
            text
            color="deep-orange"
          >
            <v-icon large>
              mdi-chevron-left
            </v-icon>
            Back
          </v-btn>

          <v-spacer />

          <v-btn
            @click="completeStep()"
            :disabled="disableNext"
            color="green"
            text
            v-show="currentCheckoutStep === 1 ? true : false"
          >
            Next
            <v-icon large>
              mdi-chevron-right
            </v-icon>
          </v-btn>

          <v-spacer></v-spacer>
        </v-row>
      </v-container>
    </v-card-actions>
  </v-card>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
export default {
  computed: {
    ...mapState("cart", [
      "currentCheckoutStep",
      "isValid",
      "order_id",
      "checkoutSteps"
    ]),

    disableNext() {
      if (this.currentStep === 1) {
        return !this.isValid;
      }
    }
  },
  methods: {
    ...mapMutations("cart", ["previousStep"]),
    ...mapActions("cart", ["completeStep"])
  }
};
</script>
