import { isArray } from "util";

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
        cartProducts: []
    },

    mutations: {
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
            console.log(model);

            if (isArray(model)) {
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
            }

            console.log(state.cartProducts);
        }
    }
};
