import Vue from "vue";
import Vuex from "vuex";
import "es6-promise/auto";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        baseUrl: "/api/",
        restoreCartDialog: false,
        checkoutDialog: false,

        productList: [],
        customerList: [],
        userList: [],
        categoryList: [],
        storeList: [],
        cartCustomer: undefined,
        cartProducts: [],
        cartsOnHold: []
        // Current state of the application lies here.
    },
    getters: {
        getCustomerById: state => id => {
            return state.customerList.find(customer => customer.id === id);
        }
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
        setUserList(state, users) {
            state.userList = users;
        },
        setCategoryList(state, categories) {
            state.categoryList = categories;
        },
        setStoreList(state, stores) {
            state.storeList = stores;
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
        },
        addOrderCustomer(state, orderCustomer) {
            let orderCustomers = state.orderCustomers;
            orderCustomers.push(orderCustomer);
            Vue.set(state, "orderCustomers", orderCustomers);
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
                        console.log(response.data);
                        context.commit(payload.mutation, response.data);
                        resolve(response.data);
                        return;
                    })
                    .catch(error => {
                        console.log(error);
                        reject(error);
                    });
            });
        },
        create(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        this.state.baseUrl + payload.model + "/create",
                        payload.data
                    )
                    .then(response => {
                        console.log(response);
                        resolve(response.data);
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
