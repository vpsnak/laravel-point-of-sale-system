import Vue from "vue";
import Vuex from "vuex";

import "es6-promise/auto";
import Cookies from "js-cookie";

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

        user: Cookies.get("user") ? JSON.parse(Cookies.get("user")) : null,

        token: Cookies.get("token") || null,

        store: Cookies.get("store") ? JSON.parse(Cookies.get("store")) : null,

        cashRegister: Cookies.get("cash_register")
            ? JSON.parse(Cookies.get("cash_register"))
            : null,

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
        authorized: state => {
            if (state.user && state.token) {
                return true;
            } else {
                return false;
            }
        },
        openedRegister: state => {
            if (state.store && state.cashRegister) {
                return true;
            } else {
                return false;
            }
        }
    },
    mutations: {
        logout(state) {
            state.user = null;
            state.token = null;
            state.cashRegister = null;
            state.store = null;

            Cookies.remove("user");
            Cookies.remove("token");
            Cookies.remove("store");
            Cookies.remove("cash_register");
        },
        setCashRegister(state, cashRegister) {
            if (cashRegister) {
                state.cashRegister = cashRegister;
                Cookies.set("cash_register", cashRegister, {
                    sameSite: "strict"
                });
            } else {
                console.log("alvanizer");
                state.cashRegister = null;
                Cookies.remove("cash_register");
            }
        },
        setStore(state, store) {
            if (store) {
                state.store = store;
                Cookies.set("store", store, {
                    sameSite: "strict"
                });
            } else {
                state.store = null;
                Cookies.remove("store");
            }
        },
        setUser(state, user) {
            state.user = user;
            Cookies.set("user", user, {
                sameSite: "strict"
            });

            if (state.user.open_register) {
                state.cashRegister = state.user.open_register.cash_register;
                state.store = state.user.open_register.cash_register.store;

                Cookies.set("cash_register", state.cashRegister, {
                    sameSite: "strict"
                });
            }
        },
        setToken(state, token) {
            state.token = "Bearer " + token;
            Cookies.set("token", "Bearer " + token, {
                sameSite: "strict"
            });
        },
        setNotification(state, notification) {
            state.notification = notification;
        },
        setProductList(state, products) {
            state.productList = products.data;
        },
        setCategoryList(state, categories) {
            state.categoryList = categories;
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
                        context.commit(
                            "setNotification",
                            response.data.notification
                        );
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                    .finally(() => {
                        context.commit("logout", {
                            root: true
                        });
                    });
            });
        },
        getAll(context, payload) {
            return new Promise((resolve, reject) => {
                let page = payload.page ? "?page=" + payload.page : "";

                axios
                    .get(this.state.baseUrl + payload.model + page)
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            context.commit(payload.mutation, response.data, {
                                root: true
                            });
                        }
                        resolve(response.data);
                    })
                    .catch(error => {
                        console.log(error.response);
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
                let page = payload.page ? "?page=" + payload.page : "";

                axios
                    .get(
                        this.state.baseUrl +
                            payload.model +
                            "/" +
                            payload.data.id +
                            "/" +
                            payload.data.model +
                            page
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
                let page = payload.page ? "?page=" + payload.page : "";

                axios
                    .post(
                        this.state.baseUrl + payload.model + "/search" + page,
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
        openCashRegister(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        this.state.baseUrl + "cash-register-logs/open",
                        payload
                    )
                    .then(response => {
                        context.commit(
                            "setCashRegister",
                            response.data.cash_register
                        );
                        context.commit(
                            "setStore",
                            response.data.cash_register.store
                        );
                        context.commit("setNotification", {
                            msg: "Cash register opened successfully",
                            type: "success"
                        });

                        resolve(true);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response,
                            type: "error"
                        };
                        context.commit("setNotification", notification);
                        reject(error);
                    });
            });
        },

        closeCashRegister(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        this.state.baseUrl + "cash-register-logs/close",
                        payload.data
                    )
                    .then(() => {
                        context.commit("setCashRegister", null);
                        context.commit("setStore", null);
                        context.commit("setNotification", {
                            msg: "Cash register closed successfully",
                            type: "success"
                        });

                        resolve(true);
                    })
                    .catch(error => {
                        let notification = {
                            msg: error.response,
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
        postRequest(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.state.baseUrl + payload.url, payload.data)
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
