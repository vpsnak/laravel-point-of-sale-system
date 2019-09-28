export default {
    namespaced: true,

    state: {
        retail: true,
        taxes: true,

        discountTypes: [
            {
                label: "None",
                value: "None"
            },
            {
                label: "Flat",
                value: "Flat"
            },
            {
                label: "Percentage",
                value: "Percentage"
            }
        ],

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
        ],
        currentCheckoutStep: 1,

        customer: undefined,
        products: [],

        discount_type: "",
        discount_amount: 0,

        shipping: {},

        order: undefined
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
        emptyCart(state) {
            state.products = [];
        },
        toggleRetail(state) {
            state.retail = !state.retail;
        },
        toggleTaxes(state) {
            state.taxes = !state.taxes;
        },
        addProduct(state, product) {
            if (_.includes(state.products, product)) {
                let index = _.findIndex(state.products, product);
                state.products[index].qty++;
            } else {
                Vue.set(product, "qty", 1);
                state.products.push(product);
            }
        },
        increaseProductQty(state, product) {
            let index = _.findIndex(state.products, product);

            state.products[index].qty++;
        },
        decreaseProductQty(state, product) {
            let index = _.findIndex(state.products, product);

            if (state.products[index].qty > 1) {
                state.products[index].qty--;
            }
        },
        setDiscount(state, model) {
            if (Array.isArray(model)) {
                state.discount_type = model.discount_type;
                state.discount_amount = model.discount_amount;
            } else {
                let index = _.findIndex(state.products, iterator => {
                    return iterator.id === model.id;
                });

                state.products[index].discount_type = model.discount_type;
                state.products[index].discount_amount = model.discount_amount;
            }
        },
        setCustomer(state, customer) {
            state.customer = customer;
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
        submitOrder({ commit, dispatch }) {
            console.log(this.state.products);

            return new Promise((resolve, reject) => {
                let payload = {
                    model: "orders",
                    data: {
                        created_by: this.state.user.id,
                        store_id: this.state.store.id,
                        status: "pending",
                        discount_type: this.state.discount_type,
                        discount_amount: this.state.discount_amount,
                        products: this.state.products,
                        tax: 5 // @TODO: needs reevaluation
                    }
                };

                dispatch("create", payload, {
                    root: true
                })
                    .then(response => {
                        console.log(response);
                        resolve(response);
                    })
                    .catch(error => {
                        console.log(error);
                        reject(error);
                    });
            });
        },
        completeStep(context, currentStep) {
            context.commit("completeStep", currentStep);
            context.commit("nextCheckoutStep");
        }
    }
};
