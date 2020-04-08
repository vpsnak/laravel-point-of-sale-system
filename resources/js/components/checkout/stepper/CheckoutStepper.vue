<template>
  <v-container class="fill-height pa-0">
    <v-row class="fill-height">
      <v-col :cols="12" class="fill-height pa-0">
        <v-stepper v-model="currentCheckoutStep" class="fill-height">
          <v-stepper-header>
            <v-stepper-step
              v-for="checkoutStep in checkoutSteps"
              :key="checkoutStep.id"
              :step="checkoutStep.id"
              :complete="checkoutStep.completed"
            >
              <span v-text="checkoutStep.name" />
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
                <v-icon
                  :color="checkoutStep.color"
                  v-text="checkoutStep.icon"
                />
                <span class="title" v-text="checkoutStep.name" />
              </v-col>
              <v-col :cols="12">
                <component :is="checkoutStep.component" />
              </v-col>
            </v-stepper-content>
            <v-container>
              <v-row no-gutters align="center" justify="center">
                <v-spacer />

                <v-btn
                  icon
                  @click="previousStep()"
                  v-show="!order_id && currentCheckoutStep === 2"
                  color="deep-orange"
                  :disabled="checkout_loading"
                >
                  <v-icon large v-text="'mdi-chevron-left'" />
                </v-btn>

                <v-spacer v-show="currentCheckoutStep !== 3" />

                <v-btn
                  @click="resetState()"
                  color="primary"
                  outlined
                  v-show="currentCheckoutStep === 3 ? true : false"
                  :disabled="checkout_loading"
                >
                  Close
                </v-btn>

                <v-btn
                  icon
                  @click="completeStep()"
                  :disabled="disableNext || checkout_loading"
                  color="primary"
                  v-show="currentCheckoutStep === 1 ? true : false"
                >
                  <v-icon large v-text="'mdi-chevron-right'" />
                </v-btn>

                <v-spacer />
              </v-row>
            </v-container>
          </v-stepper-items>
        </v-stepper>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
export default {
  computed: {
    ...mapState("cart", [
      "checkout_loading",
      "currentCheckoutStep",
      "is_valid",
      "order_id",
      "checkoutSteps",
      "method",
    ]),

    disableNext() {
      if (this.currentCheckoutStep === 1 && this.method !== "retail") {
        return !this.is_valid;
      } else {
        return false;
      }
    },
  },
  methods: {
    ...mapMutations("cart", ["previousStep", "resetState"]),
    ...mapActions("cart", ["completeStep"]),
  },
};
</script>
