export default {
    namespaced: true,
    state: {
        retail: false,
        currentCheckoutStep: 1,

        checkoutSteps: [
            {
                name: "Shipping options",
                icon: "local_shipping",
                component: "shippingStep",
                showIfRetail: false,
                completed: false
            },
            {
                name: "Payment",
                icon: "payment",
                component: "paymentStep",
                showIfRetail: true,
                completed: false
            },
            {
                name: "Completion",
                icon: "check_circle",
                component: "completion",
                showIfRetail: true,
                completed: false
            }
        ]
    },

    getters: {
        getCheckoutSteps(state) {
            return state.retail
                ? state.checkoutSteps.filter(
                      checkoutStep => checkoutStep.showIfRetail
                  )
                : state.checkoutSteps;
        }
    },

    mutations: {
        toggleRetail(state) {
            state.retail = !state.retail;
        },
        completeStep(state, currentStep) {
            let result = _.find(state.checkoutSteps, currentStep);

            result.completed = true;
            _.merge(result, currentStep);
        },
        nextCheckoutStep(state) {
            state.currentCheckoutStep++;
        }
    },

    actions: {
        completeStep(context, currentStep) {
            context.commit("completeStep", currentStep);
            context.commit("nextCheckoutStep");
        }
    }
};
