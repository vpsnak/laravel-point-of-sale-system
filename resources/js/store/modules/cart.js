export default {
    namespaced: true,

    state: {
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
                id: 1,
                name: "Delivery options",
                icon: "local_shipping",
                component: "shippingStep",
                completed: false
            },
            {
                id: 2,
                name: "Payment",
                icon: "payment",
                component: "paymentStep",
                completed: false
            },
            {
                id: 3,
                name: "Order Completed",
                icon: "check_circle",
                component: "completion",
                completed: false
            }
        ],
        currentCheckoutStep: 1,

        customer: undefined,
        products: [],
        cart_price: 0,

        discount_type: "",
        discount_amount: 0,

        shipping: {
            address: undefined,
            notes: "",
            method: "retail",
            date: undefined,
            timeSlotLabel: null,
            timeSlotCost: 0
        },

        order: undefined
    },

    mutations: {
        addProduct(state, product) {
            let index = _.findIndex(state.products, productState => {
                return productState.id === product.id;
            });

            if (index != -1) {
                state.products[index].qty++;
            } else {
                Vue.set(product, "qty", 1);
                state.products.push(product);
            }
        },
        increaseProductQty(state, product) {
            let index = _.findIndex(state.products, productState => {
                return productState.id === product.id;
            });

            if (index != -1) {
                state.products[index].qty++;
            }
        },
        decreaseProductQty(state, product) {
            let index = _.findIndex(state.products, productState => {
                return productState.id === product.id;
            });

            if (index != -1 && state.products[index].qty > 1) {
                state.products[index].qty--;
            }
        },
        setDiscount(state, model) {
            if (Array.isArray(model)) {
                state.discount_type = model.discount_type;

                state.discount_amount = parseFloat(model.discount_amount);
            } else {
                let index = _.findIndex(state.products, iterator => {
                    return iterator.id === model.id;
                });

                state.products[index].discount_type = model.discount_type;
                state.products[index].discount_amount = parseFloat(
                    model.discount_amount
                );
            }
        },
        setCustomer(state, customer) {
            state.customer = customer;
        },
        completeStep(state) {
            let index = -1 + state.currentCheckoutStep;
            state.checkoutSteps[index].completed = true;
        },
        nextCheckoutStep(state) {
            state.currentCheckoutStep++;
        },
        setOrder(state, order) {
            state.order = order;
        },
        setCartPrice(state, cartPrice) {
            state.cart_price = cartPrice;
        },
        resetState(state) {
            state.currentCheckoutStep = 1;
            state.customer = undefined;
            state.products = [];
            state.discount_type = "";
            state.discount_amount = 0;
            state.order = undefined;
            state.total_price = 0;

            state.checkoutSteps.forEach(checkoutStep => {
                checkoutStep.completed = false;
            });

            state.shipping = {
                address: undefined,
                method: "retail",
                date: undefined,
                timeSlotLabel: null,
                timeSlotCost: 0
            };
        },
        resetShipping(state) {
            let notes = state.shipping.notes;
            state.shipping = {
                notes: notes,
                address: undefined,
                method: "retail",
                date: undefined,
                timeSlotLabel: null,
                timeSlotCost: 0
            };

            state.currentCheckoutStep = 1;
            state.checkoutSteps.forEach(checkoutStep => {
                checkoutStep.completed = false;
            });
        }
    },
    actions: {
        submitOrder({ state, commit, dispatch }) {
            return new Promise((resolve, reject) => {
                let payload = {
                    model: "orders",
                    data: {
                        customer_id: state.customer ? state.customer.id : "",
                        created_by: this.state.user.id,
                        store_id: this.state.store.id,
                        status: "pending",
                        discount_type: state.discount_type,
                        discount_amount: state.discount_amount,
                        products: state.products
                    }
                };

                dispatch("create", payload, {
                    root: true
                })
                    .then(response => {
                        commit("setOrder", response);
                        resolve(response);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        completeStep(context) {
            context.commit("completeStep");
            context.commit("nextCheckoutStep");
        }
    }
};
