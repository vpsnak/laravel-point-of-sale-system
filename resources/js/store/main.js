import Vue from "vue";
import Vuex from "vuex";
import "es6-promise/auto";

//modules
import topMenu from "./menu/topMenu";
import checkout from "./modules/checkout";
import cart from "./modules/cart";
import payment from "./modules/payment";
import endpoints from "./modules/endpoints";

Vue.use(Vuex);

const namespaced = true;

export default new Vuex.Store({
    namespaced,
    modules: {
        topMenu,
        checkout,
        cart,
        payment,
        endpoints
    },
    state: {
        baseUrl: "/api/",
        restoreCartDialog: false,
        checkoutDialog: false,
        productList: [],
        customerList: [],
        userList: [],
        categoryList: [],
        storeList: [],
        cartsOnHold: [],
        cartCustomer: undefined

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
        setUserList(state, users) {
            state.userList = users;
        },
        setCategoryList(state, categories) {
            state.categoryList = categories;
        },
        setStoreList(state, stores) {
            state.storeList = stores;
        },
        setCartsOnHold(state, cartsOnHold) {
            state.cartsOnHold = cartsOnHold;
        }
    },
    actions: {
        getAll(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.state.baseUrl + payload.model)
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            console.log("mutation: " + payload.mutation);
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
                    })
                    .catch(error => {
                        console.log(error);
                        reject(error);
                    });
            });
        },
        getOne(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        this.state.baseUrl +
                            payload.model +
                            "/" +
                            payload.data.id
                    )
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            console.log("mutation: " + payload.mutation);
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
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
                        if (_.has(payload, "mutation")) {
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
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
                        if (_.has(payload, "mutation")) {
                            console.log("mutation: " + payload.mutation);
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
                    })
                    .catch(error => {
                        console.log(error);
                        reject(error);
                    });
            });
        },
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(
                        this.state.baseUrl + payload.model + "/" + payload.id
                    )
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
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