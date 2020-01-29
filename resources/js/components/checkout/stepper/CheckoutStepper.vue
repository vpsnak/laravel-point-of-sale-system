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
                <v-row align="center" justify="center">
                    <v-col align="center" justify="center">
                        <v-icon large :color="checkoutStep.color">
                            {{ checkoutStep.icon }}
                        </v-icon>
                        <h3>
                            {{ checkoutStep.name }}
                        </h3>
                    </v-col>
                </v-row>

                <component
                    :is="checkoutStep.component"
                    :key="currentCheckoutStep"
                    style="overflow-y:auto;overflow-x:hidden;height:65vh"
                />
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
