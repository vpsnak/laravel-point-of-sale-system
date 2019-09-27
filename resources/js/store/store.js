import Vue from "vue";
import Vuex from "vuex";
import "es6-promise/auto";

//modules
import topMenu from "./menu/topMenu";
import checkout from "./modules/checkout";
import cart from "./modules/cart";
import payment from "./modules/payment";
import endpoints from "./modules/endpoints";
import datatable from "./modules/datatable";

Vue.use(Vuex);

const namespaced = true;

export default new Vuex.Store({
    namespaced,
    modules: {
        topMenu,
        checkout,
        cart,
        payment,
        datatable,
        endpoints
    },
    state: {
        baseUrl: "/api/",
        user: {
            id: 1,
            name: 'Mega Lola',
            email: 'example@example.com',
            token: null
        },
        restoreCartDialog: false,
        checkoutDialog: false,
        productList: [],
        userList: [],
        categoryList: [],
        storeList: [],
        cartsOnHold: []
    },
    getters: {
        // Compute derived state based on the current state. More like computed property.
    },
    mutations: {
        setUser(state, users) {
            state.userList = users;
        },
        setProductList(state, products) {
            state.productList = products;
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
        },
        getRequest(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.state.baseUrl + payload.url)
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
        deleteRequest(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(this.state.baseUrl + payload.url)
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
        }
    }
});

export const store = new Vuex.Store({});
