export default {
    namespaced: true,

    state: {
        checkoutDialog: false,
        retail: true,
        refundLoading: false,
        paymentLoading: false,
        completeOrderLoading: false,

        isValid: false,
        isValidCheckout: false,
        discount_error: false,

        discountErrors: [],

        locations: [
            { id: 1, label: "Funeral Home" },
            { id: 2, label: "Church" },
            { id: 3, label: "Reception Hall" },
            { id: 4, label: "Hospital" },
            { id: 5, label: "Business" },
            { id: 6, label: "Home" },
            { id: 7, label: "Apartment" },
            { id: 8, label: "Hotel" },
            { id: 9, label: "International City" },
            { id: 10, label: "School" },
            { id: 11, label: "Other" },
            { id: 12, label: "Pick Up or Will Call" }
        ],

        occasions: [
            { id: 1, label: "Wedding" },
            { id: 2, label: "Birthday" },
            { id: 3, label: "Holiday" },
            { id: 4, label: "Anniversary" },
            { id: 5, label: "Business" },
            { id: 6, label: "New Baby" },
            { id: 7, label: "Get Well" },
            { id: 8, label: "Funeral" },
            { id: 9, label: "Other" }
        ],

        discountTypes: [
            {
                label: "None",
                value: null
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
                name: "Cash & Carry",
                icon: "mdi-cart-arrow-right",
                color: "primary",
                component: "shippingStep",
                completed: true
            },
            {
                id: 2,
                name: "Payment",
                icon: "mdi-cash-register",
                color: "",
                component: "paymentStep",
                completed: false
            },
            {
                id: 3,
                name: "Order Completed",
                icon: "check_circle",
                color: "",
                component: "completion",
                completed: false
            }
        ],
        currentCheckoutStep: 2,

        customer: null,
        products: [],
        cart_price: 0,

        discount_type: "",
        discount_amount: 0,

        shipping: {
            pickup_point: undefined,
            address: undefined,
            notes: "",
            method: "retail",
            date: undefined,
            timeSlotLabel: null,
            cost: 0,
            location: null,
            occasion: 9
        },

        billing_address: undefined,

        order: {
            id: null,
            customer_id: null,
            status: null,
            discount_type: null,
            discount_amount: null,
            tax: null,
            subtotal: null,
            change: null,
            notes: null,
            shipping_type: null,
            shipping_cost: null,
            shipping_address_id: null,
            billing_address_id: null,
            store_pickup_id: null,
            delivery_date: null,
            delivery_slot: null,
            location: null,
            occasion: null,
            store_id: {},
            created_by: {},
            created_at: null,
            updated_at: null,
            total: null,
            total_without_tax: null,
            total_paid: null,
            items: [],
            payments: [],
            customer: {},
            shipping_address: null,
            store_pickup: null
        }
    },

    mutations: {
        setCheckoutDialog(state, value) {
            state.checkoutDialog = value;
        },
        setPaymentHistory(state, value) {
            if (Array.isArray(value)) {
                state.order.payments = value;
            } else {
                state.order.payments.push(value);
            }
        },
        isValidDiscount(state) {
            let result = true;

            if (state.discount_error) {
                result = false;
            }

            state.products.forEach(product => {
                if (product.discount_error) {
                    result = false;
                }
            });

            state.isValidCheckout = result;
        },
        setShippingCost(state, value) {
            state.shipping.cost = value;
        },
        toggleRetail(state, retail) {
            if (retail) {
                state.retail = true;
                state.checkoutSteps[0].completed = true;
                state.currentCheckoutStep = 2;
            } else {
                state.retail = false;
                state.checkoutSteps[0].completed = false;
                state.currentCheckoutStep = 1;
            }
        },
        addProduct(state, product) {
            let index = _.findIndex(state.products, productState => {
                return productState.id === product.id;
            });

            if (index != -1) {
                state.products[index].qty++;
            } else {
                Vue.set(product, "qty", 1);
                state.products.push(_.cloneDeep(product));
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
        setCustomer(state, customer) {
            if (_.isObjectLike(customer)) {
                state.customer = customer;
            } else {
                state.customer = null;
            }
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
            state.checkoutDialog = false;
            state.paymentLoading = false;
            state.customer = null;

            state.products = [];
            state.discount_type = "";
            state.discount_amount = 0;
            state.order = {
                id: null,
                customer_id: null,
                status: null,
                discount_type: null,
                discount_amount: null,
                tax: null,
                subtotal: null,
                change: null,
                notes: null,
                shipping_type: null,
                shipping_cost: null,
                shipping_address_id: null,
                billing_address_id: null,
                store_pickup_id: null,
                delivery_date: null,
                delivery_slot: null,
                location: null,
                occasion: null,
                store_id: {},
                created_by: {},
                created_at: null,
                updated_at: null,
                total: null,
                total_without_tax: null,
                total_paid: null,
                items: [],
                payments: [],
                customer: {},
                shipping_address: null,
                store_pickup: null
            };

            state.total_price = 0;

            state.checkoutSteps[0].name = "Cash & Carry";
            state.checkoutSteps[0].icon = "mdi-cart-arrow-right";
            state.checkoutSteps[0].color = "primary";
            state.currentCheckoutStep = 2;
            state.checkoutSteps.forEach(checkoutStep => {
                checkoutStep.completed = false;
            });

            state.shipping = {
                address: undefined,
                method: "retail",
                date: undefined,
                timeSlotLabel: null,
                cost: 0,
                location: null,
                occasion: 9
            };
            state.billing_address = undefined;
            state.completeOrderLoading = false;
        },
        resetShipping(state) {
            let notes = state.shipping.notes;
            state.shipping = {
                notes: notes,
                address: undefined,
                method: "retail",
                date: undefined,
                timeSlotLabel: null,
                cost: 0,
                location: null,
                occasion: 9
            };
            state.billing_address = undefined;

            state.currentCheckoutStep = 1;
            state.checkoutSteps.forEach(checkoutStep => {
                checkoutStep.completed = false;
            });
            state.checkoutSteps[0].name = "Cash & Carry";
            state.checkoutSteps[0].icon = "mdi-cart-arrow-right";
            state.checkoutSteps[0].color = "primary";
        }
    },
    actions: {
        mailReceipt({ commit, state, rootState }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${rootState.baseUrl}mail-receipt/${state.order.id}`,
                        payload
                    )
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };

                        commit("setNotification", notification, {
                            root: true
                        });
                        resolve(response);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        commit("setNotification", notification, {
                            root: true
                        });
                        reject(error);
                    });
            });
        },
        saveGuestEmail({ commit, rootState }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(rootState.baseUrl + "guest-email/create", payload)
                    .then(response => {
                        resolve(response);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        commit("setNotification", notification, {
                            root: true
                        });
                        reject(error);
                    });
            });
        },
        createReceipt(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `${this.state.baseUrl}receipts/create/${payload.data.order_id}`
                    )
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };
                        context.commit("setNotification", notification);
                        resolve(response.data);
                    })
                    .catch(error => {
                        if (error.response) {
                            let notification = {
                                msg: error.response.data.errors,
                                type: "error"
                            };
                            context.commit("setNotification", notification);
                        } else {
                            let notification = {
                                msg: "Unexpected error occured",
                                type: "error"
                            };
                            context.commit("setNotification", notification);
                        }
                        reject(error);
                    });
            });
        },
        submitOrder({ state, commit, dispatch }) {
            return new Promise((resolve, reject) => {
                let payload = {
                    model: "orders",
                    data: {
                        customer_id: state.customer ? state.customer.id : "",
                        store_id: this.state.store.id,
                        status: "pending",
                        discount_type: state.discount_type,
                        discount_amount: state.discount_amount,
                        products: state.products,
                        shipping: state.shipping,
                        billing_address: state.billing_address
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
