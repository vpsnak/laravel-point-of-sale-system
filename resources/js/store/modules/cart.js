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

        customer: undefined,
        products: [],

        discount_type: "",
        discount_amount: 0,

        shipping: {},

        order: undefined
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
        }
    },
    actions: {
        submitOrder({ commit, dispatch }) {
            return new Promise((resolve, reject) => {
                let payload = {
                    model: "orders",
                    data: { ...this.state, user_id: this.state.user.id }
                }

                dispatch(
                    "create", payload,
                    {
                        root: true
                    }
                )
                    .then(response => {
                        console.log(response);
                        resolve(response);
                    })
                    .catch(error => {
                        console.log(error);
                        reject(error);
                    });
            });
        }
    }
};
