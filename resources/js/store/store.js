import Vue from "vue";
import Vuex from "vuex";
import "es6-promise/auto";

//modules
import topMenu from "./menu/topMenu";
import cart from "./modules/cart";
import endpoints from "./modules/endpoints";
import datatable from "./modules/datatable";

Vue.use(Vuex);

const namespaced = true;

export default new Vuex.Store({
    namespaced,
    modules: {
        topMenu,
        cart,
        datatable,
        endpoints
    },
    state: {
        baseUrl: "/api/",

        user: {
            id: 1,
            name: "asd",
            email: "asd@asd.asd"
        },

        token: null,

        store: {
            id: null,
            name: "",
            tax: {}
        },

        cashRegister: {
            id: null,
            name: ""
        },

        // notification
        notification: {
            msg: "",
            type: undefined
        },

        // dialogs
        cartRestoreDialog: false,
        checkoutDialog: false,

        productList: [],
        categoryList: [],
        storeList: []
    },
    getters: {
        // Compute derived state based on the current state. More like computed property.
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        setToken(state, token) {
            state.token = token;
        },
        setNotification(state, notification) {
            state.notification = notification;
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
        closeAllDialogs(state) {
            state.cartRestoreDialog = false;
            state.checkoutDialog = false;
        }
    },
    actions: {
        login(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.state.baseUrl + "auth/login", payload)
                    .then(response => {
                        console.log(response);
                        context.commit("setUser", response.data.user, {
                            root: true
                        });
                        context.commit("setToken", response.data.token, {
                            root: true
                        });
                        context.commit(
                            "setNotification",
                            response.data.notification
                        );

                        resolve(response.data);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
                        reject(error);
                    });
            });
        },
        logout(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.state.baseUrl + "auth/logout")
                    .then(response => {
                        context.commit(setUser, response.data.user, {
                            root: true
                        });
                        context.commit(
                            "setNotification",
                            response.notification
                        );
                        resolve(response.data);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
                        reject(error);
                    });
            });
        },
        getAll(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.state.baseUrl + payload.model)
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
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
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
                        reject(error);
                    });
            });
        },
        getManyByOne(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        this.state.baseUrl +
                            payload.model +
                            "/" +
                            payload.data.id +
                            "/" +
                            payload.data.model
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
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
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
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
                        reject(error);
                    });
            });
        },
        create(context, payload) {
            return new Promise((resolve, reject) => {
                payload.data.created_by = this.state.user.id;

                axios
                    .post(
                        this.state.baseUrl + payload.model + "/create",
                        payload.data
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
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
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
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
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
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
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
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response.data.errors,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
                        reject(error);
                    });
            });
        }
    }
});

export const store = new Vuex.Store({});
