import Vue from "vue";
import Vuex from "vuex";

import "es6-promise/auto";
import Cookies from "js-cookie";
import router from "../plugins/router";
//modules
import menu from "./modules/menu";
import cart from "./modules/cart";
import endpoints from "./modules/endpoints";
import datatable from "./modules/datatable";

Vue.use(Vuex);

const namespaced = true;

export default new Vuex.Store({
    namespaced,
    modules: {
        menu,
        cart,
        datatable,
        endpoints
    },
    state: {
        baseUrl: "/api/",

        user: Cookies.get("user") ? JSON.parse(Cookies.get("user")) : null,

        token: Cookies.get("token") || null,

        store: null,
        cashRegister: null,

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
            if (state.token && state.user) {
                return true;
            } else {
                return false;
            }
        },
        role: state => {
            if (state.user) {
                return state.user.roles[0].name;
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

            if (router.currentRoute.name !== "login") {
                router.push({
                    name: "login"
                });
            }
        },
        setCashRegister(state, cashRegister) {
            if (cashRegister) {
                state.cashRegister = cashRegister;
            } else {
                state.cashRegister = null;
            }
        },
        setStore(state, store) {
            if (store) {
                state.store = store;
            } else {
                state.store = null;
            }
        },
        setUser(state, user) {
            if (user) {
                state.user = user;
                Cookies.set("user", state.user, {
                    secure: true,
                    sameSite: "strict"
                });
            } else {
                state.user = null;
                Cookies.remove("user");
            }
        },
        setToken(state, token) {
            if (token) {
                state.token = "Bearer " + token;
                Cookies.set("token", state.token, {
                    secure: true,
                    sameSite: "strict"
                });
            } else {
                state.token = null;
                Cookies.remove("token");
            }
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
                        let notification = {
                            msg: response.data.info,
                            type: "info"
                        };

                        context.commit("setUser", response.data.user);
                        context.commit("setToken", response.data.token);
                        context.commit("setNotification", notification);

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
                        let notification = {
                            msg: response.data.info,
                            type: "info"
                        };
                        context.commit("setNotification", notification);
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                    .finally(() => {
                        context.commit("logout");
                    });
            });
        },
        verifySelf(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.state.baseUrl + "auth/verify/", payload)
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "info"
                        };
                        context.commit("setNotification", notification);
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
        changeSelfPwd(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.state.baseUrl + "auth/password/", payload)
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };
                        context.commit("setNotification", notification);
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
        changeUserPwd(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.state.baseUrl + "users/password/", payload)
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };
                        context.commit("setNotification", notification);
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
                let page = payload.page ? "?page=" + payload.page : "";

                axios
                    .get(this.state.baseUrl + payload.model + page)
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            context.commit(
                                payload.mutation,
                                response.data.data || response.data
                            );
                        }

                        if (payload.dataTable) {
                            resolve(response.data);
                        } else {
                            resolve(response.data.data || response.data);
                        }
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
                        resolve(response.data.data || response.data);
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
                            context.commit(
                                payload.mutation,
                                response.data.data
                            );
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
                            context.commit(
                                payload.mutation,
                                response.data.data || response.data
                            );
                        }
                        if (payload.dataTable) {
                            resolve(response.data);
                        } else {
                            resolve(response.data.data || response.data);
                        }
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
                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };

                        context.commit(
                            "setCashRegister",
                            response.data.cashRegister.cash_register
                        );
                        context.commit(
                            "setStore",
                            response.data.cashRegister.cash_register.store
                        );
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
                                msg:
                                    "Unexpected error occured. Check console for more info",
                                type: "error"
                            };
                            console.log(error);
                            context.commit("setNotification", notification);
                        }

                        reject(error);
                    });
            });
        },
        logoutCashRegister(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.state.baseUrl + "cash-register-logs/logout")
                    .then(response => {
                        if (
                            router.currentRoute.name === "sales" ||
                            router.currentRoute.name === "orders"
                        ) {
                            router.push({
                                name: "dashboard"
                            });
                        }

                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };

                        context.commit("setCashRegister", null);
                        context.commit("setStore", null);
                        context.commit("setNotification", notification);

                        resolve(true);
                    })
                    .catch(error => {
                        if (error.response) {
                            let notification = {
                                msg: error.response.data.errors,
                                type: "error"
                            };
                            context.commit("setNotification", notification);

                            context.commit("setCashRegister", null);
                            context.commit("setStore", null);
                            context.commit("setNotification", notification);
                        } else {
                            let notification = {
                                msg:
                                    "Unexpected error occured. Check console for more info",
                                type: "error"
                            };
                            console.log(error);
                            context.commit("setNotification", notification);
                        }

                        reject(error);
                    });
            });
        },
        cashRegisterAmount(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        this.state.baseUrl +
                            `cash-register-logs/${context.state.cashRegister.id}/amount`
                    )
                    .then(response => {
                        resolve(response.data);
                    });
            });
        },
        retrieveCashRegister(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get(this.state.baseUrl + "cash-register-logs/retrieve")
                    .then(response => {
                        if (response.data !== 0) {
                            if (
                                !context.state.cashRegister ||
                                !context.state.store
                            ) {
                                let notification = {
                                    msg: `Your open session with cash register: <b>${response.data.cashRegister.cash_register.name}</b> has been restored`,
                                    type: "info"
                                };

                                context.commit("setNotification", notification);
                            }

                            context.commit(
                                "setCashRegister",
                                response.data.cashRegister.cash_register
                            );
                            context.commit(
                                "setStore",
                                response.data.cashRegister.cash_register.store
                            );
                        } else {
                            if (
                                context.state.store ||
                                context.state.cashRegister
                            ) {
                                let notification = {
                                    msg: `Your session with cash register: <b>${context.state.cashRegister.name}</b> has been terminated`,
                                    type: "error"
                                };
                                context.commit("setNotification", notification);

                                if (
                                    router.currentRoute.name === "sales" ||
                                    router.currentRoute.name === "orders"
                                ) {
                                    router.push({
                                        name: "dashboard"
                                    });
                                }
                            }
                            context.commit("setCashRegister", null);
                            context.commit("setStore", null);
                        }
                        resolve(true);
                    })
                    .catch(error => {
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
                    .then(response => {
                        if (
                            router.currentRoute.name === "sales" ||
                            router.currentRoute.name === "orders"
                        ) {
                            router.push({
                                name: "dashboard"
                            });
                        }

                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };

                        context.commit("setCashRegister", null);
                        context.commit("setStore", null);
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
                                msg:
                                    "Unexpected error occured. Check console for more info",
                                type: "error"
                            };
                            console.log(error);
                            context.commit("setNotification", notification);
                        }

                        reject(error);
                    });
            });
        },

        setRole(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(this.state.baseUrl + "roles/set", payload.data)
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };
                        context.commit("setNotification", notification);

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
                let options;
                if (payload.data instanceof FormData) {
                    options = {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    };
                }
                axios
                    .post(
                        this.state.baseUrl + payload.model + "/create",
                        payload.data,
                        options
                    )
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            context.commit(
                                payload.mutation,
                                response.data.data || response.data
                            );
                        }
                        resolve(response.data.data || response.data);
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

                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };
                        context.commit("setNotification", notification);

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
                            context.commit(
                                payload.mutation,
                                response.data.data || response.data
                            );
                        }
                        resolve(response.data.data || response.data);
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
                            context.commit(
                                payload.mutation,
                                response.data.data || response.data
                            );
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
