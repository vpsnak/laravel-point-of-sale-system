import Vue from "vue";
import Vuex from "vuex";
import "es6-promise/auto";
import { ENGINE_METHOD_PKEY_ASN1_METHS } from "constants";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        baseUrl: "/api/",
        productList: [],
        customerList: [],
        cartProducts: []
        // Current state of the application lies here.
    },
    getters: {
        // Compute derived state based on the current state. More like computed property.
    },
    mutations: {
        // Mutate the current state
        setProductList(state, products) {
            state.productList = products;
        },
        setCustomerList(state, customers) {
            state.customerList = customers;
        },

        // shopping cart mutations
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
        }
    },
    actions: {
        getAll(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.state.baseUrl + payload.model)
                    .then(response => {
                        console.log(response);
                        resolve(
                            context.commit(payload.mutation, response.data)
                        );
                    })
                    .catch(error => {
                        console.log(error);
                        reject(error);
                    });
            });
        },
        search(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        this.state.baseUrl + payload.model + "/search",
                        payload
                    )
                    .then(response => {
                        console.log(response);
                        resolve(
                            context.commit(payload.mutation, response.data)
                        );
                    })
                    .catch(error => {
                        console.log(error);
                        reject(error);
                    });
            });
        }
    }
});

export const store = new Vuex.Store({});
