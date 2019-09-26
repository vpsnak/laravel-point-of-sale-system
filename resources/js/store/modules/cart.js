export default {
    namespaced: true,

    state: {
        discountTypes: [
            {
                label: "None",
                value: "none"
            },
            {
                label: "Flat",
                value: "flat"
            },
            {
                label: "Percentage",
                value: "percentage"
            }
        ],
        customer: undefined,
        cartProducts: [],
        subtotal: 0,
        discount: 0,
        order: undefined,
        shipping: "",
        retail: true,
        taxes: true
    },

    getters: {
        getOrderData(state) {
            return {
                customer_id: state.customer.id,
                user_id: 1,
                // discount: this.totalDiscount,
                shipping_type: "shipping",
                shipping_cost: 0,
                // tax: this.tax,
                // subtotal: this.subTotal,
                note: "",
                items: state.cartProducts
            };
        }
    },

    mutations: {
        emptyCart(state) {
            state.cartProducts = [];
        },
        toggleRetail(state) {
            state.retail = !state.retail;
        },
        toggleTaxes(state) {
            state.taxes = !state.taxes;
        },
        addCartProduct(state, cartProduct) {
            if (_.includes(state.cartProducts, cartProduct)) {
                let index = _.findIndex(state.cartProducts, cartProduct);
                state.cartProducts[index].qty++;
            } else {
                Vue.set(cartProduct, "qty", 1);
                state.cartProducts.push(cartProduct);
            }
        },
        increaseCartProductQty(state, cartProduct) {
            let index = _.findIndex(state.cartProducts, cartProduct);
            state.cartProducts[index].qty++;
        },
        decreaseCartProductQty(state, cartProduct) {
            let index = _.findIndex(state.cartProducts, cartProduct);

            if (state.cartProducts[index].qty > 1) {
                state.cartProducts[index].qty--;
            }
        },
        setDiscount(state, model) {
            if (Array.isArray(model)) {
                Vue.set(
                    state.cartProducts,
                    "discount_type",
                    model.discount_type
                );
                Vue.set(
                    state.cartProducts,
                    "discount_amount",
                    model.discount_amount
                );
            } else {
                let index = _.findIndex(state.cartProducts, iterator => {
                    return iterator.id === model.id;
                });

                state.cartProducts[index] = model;
            }
        },
        setCustomer(state, customer) {
            state.customer = customer;
        }
    },
    actions: {
        submitOrder({ dispatch }) {
            return new Promise((resolve, reject) => {
                dispatch(
                    "create",
                    {
                        model: "orders",
                        data: this.getOrderData
                    },
                    { root: true }
                )
                    .then(() => {
                        console.log("success");
                        resolve();
                    })
                    .catch(e => {
                        console.log("fail");
                        reject();
                    });
            });
        }
    }
};
