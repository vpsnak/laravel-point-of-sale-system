import Vue from "vue";
import Vuex from "vuex";

import "es6-promise/auto";
import Cookies from "js-cookie";
import router from "../plugins/router";
//modules
import config from "./modules/config";
import menu from "./modules/menu";
import cart from "./modules/cart";
import datatable from "./modules/datatable";
import dialog from "./modules/dialog";
import icons from "./modules/icons";

Vue.use(Vuex);

export default new Vuex.Store({
    strict: false,
    modules: {
        config,
        menu,
        cart,
        datatable,
        dialog,
        icons
    },
    state: {
        user: Cookies.get("user") ? JSON.parse(Cookies.get("user")) : null,
        token: Cookies.get("token") || null,

        store: null,
        cashRegister: null,

        // notification
        notification: {
            msg: "",
            type: ""
        },

        productList: [],
        storeList: []
    },
    getters: {
        auth: state => {
            if (state.user && state.token) {
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
        setProductList(state, value) {
            state.productList = value;
        },
        logout(state) {
            if (_.has(state.user, "id")) {
                state.config.echo.leave(`user.${state.user.id}`);
            }

            Cookies.remove("user");
            Cookies.remove("token");
            Cookies.remove("store");
            Cookies.remove("cash_register");

            state.user = null;
            state.token = null;
            state.cashRegister = null;
            state.store = null;
            state.menu.top_menu = [];
            state.menu.side_menu = [];

            if (router.currentRoute.name !== "login") {
                router.push({ name: "login" });
            }
        },
        setCashRegister(state, cashRegister) {
            state.cashRegister = cashRegister;
        },
        setStore(state, store) {
            state.store = store;
        },
        setUser(state, user) {
            if (user) {
                state.user = user;
                Cookies.set("user", user, {
                    secure:
                        state.config.app_env !== "development" ? true : false,
                    sameSite: "strict"
                });
            } else {
                state.user = null;
                Cookies.remove("user");
            }
        },
        setToken(state, token) {
            if (token) {
                state.token = `Bearer ${token}`;

                Cookies.set("token", state.token, {
                    secure:
                        state.config.app_env !== "development" ? true : false,
                    sameSite: "strict"
                });
            } else {
                state.token = null;
                window.axios.defaults.headers.common["Authorization"] = null;

                Cookies.remove("token");
            }
        },
        setNotification(state, notification) {
            state.notification = notification;
        },
        setProductList(state, products) {
            state.productList = products;
        }
    },
    actions: {
        login(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${context.state.config.base_url}/auth/login`,
                        payload
                    )
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "info"
                        };

                        window.axios.defaults.headers.common[
                            "Authorization"
                        ] = `Bearer ${response.data.token}`;

                        context.commit("setToken", response.data.token);
                        context.commit("setUser", response.data.user);
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
                    .get(`${context.state.config.base_url}/auth/logout`)
                    .then(response => {
                        context.commit("config/resetLoad");
                        context.commit("setNotification", {
                            msg: response.data.info,
                            type: "info"
                        });
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
                    .post(
                        `${context.state.config.base_url}/auth/verify`,
                        payload
                    )
                    .then(() => {
                        resolve(true);
                    })
                    .catch(error => {
                        if (_.has(error, ".response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }

                        reject(error);
                    });
            });
        },
        changeSelfPwd(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${context.state.config.base_url}/auth/password`,
                        payload
                    )
                    .then(response => {
                        context.commit("setNotification", {
                            msg: response.data.info,
                            type: "success"
                        });
                        resolve(response.data);
                    })
                    .catch(error => {
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }

                        reject(error);
                    });
            });
        },
        changeUserPwd(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${context.state.config.base_url}/users/password`,
                        payload
                    )
                    .then(response => {
                        context.commit("setNotification", {
                            msg: response.data.info,
                            type: "success"
                        });
                        resolve(response.data);
                    })
                    .catch(error => {
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        },
        getAll(context, payload) {
            return new Promise((resolve, reject) => {
                let page = payload.page ? "?page=" + payload.page : "";

                axios
                    .get(
                        `${context.state.config.base_url}/${payload.model}${page}`
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
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        },
        getOne(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `${context.state.config.base_url}/${payload.model}/get/${payload.data.id}`
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
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        },
        getManyByOne(context, payload) {
            return new Promise((resolve, reject) => {
                let page = payload.page ? "?page=" + payload.page : "";

                axios
                    .get(
                        `${context.state.config.base_url}/${payload.model}/${payload.data.id}/${payload.data.model}${page}`
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
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        },
        search(context, payload) {
            return new Promise((resolve, reject) => {
                let page = payload.page ? "?page=" + payload.page : "";

                axios
                    .post(
                        `${context.state.config.base_url}/${payload.model}/search${page}`,
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
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        },
        openCashRegister(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${context.state.config.base_url}/cash-register-logs/open`,
                        payload
                    )
                    .then(response => {
                        context.commit(
                            "setCashRegister",
                            response.data.cashRegister.cash_register
                        );
                        context.commit(
                            "setStore",
                            response.data.cashRegister.cash_register.store
                        );
                        context.commit("setNotification", {
                            msg: response.data.info,
                            type: "success"
                        });

                        resolve(response.data);
                    })
                    .catch(error => {
                        if (error.response) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            context.commit("setNotification", {
                                msg: "Unexpected error occured",
                                type: "error"
                            });
                        }

                        reject(error);
                    });
            });
        },
        logoutCashRegister(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `${context.state.config.base_url}/cash-register-logs/logout`
                    )
                    .then(response => {
                        if (
                            router.currentRoute.name === ("sales" || "orders")
                        ) {
                            router.push({ name: "dashboard" });
                        }

                        context.commit("setCashRegister", null);
                        context.commit("setStore", null);
                        context.commit("setNotification", {
                            msg: response.data.info,
                            type: "success"
                        });

                        resolve(true);
                    })
                    .catch(error => {
                        if (error.response) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });

                            context.commit("setCashRegister", null);
                            context.commit("setStore", null);
                            context.commit("setNotification", notification);
                        } else {
                            context.commit("setNotification", {
                                msg: "Unexpected error occured",
                                type: "error"
                            });
                        }

                        reject(error);
                    });
            });
        },
        cashRegisterAmount(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get(
                        `${context.state.config.base_url}/cash-register-logs/${context.state.cashRegister.id}/amount`
                    )
                    .catch(error => {
                        reject(error.response);
                    })
                    .then(response => {
                        resolve(response.data);
                    });
            });
        },

        closeCashRegister(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${context.state.config.base_url}/cash-register-logs/close`,
                        payload.data
                    )
                    .then(response => {
                        if (
                            router.currentRoute.name === ("sales" || "orders")
                        ) {
                            router.push({ name: "dashboard" });
                        }

                        context.commit("setCashRegister", null);
                        context.commit("setStore", null);
                        context.commit("setNotification", {
                            msg: response.data.info,
                            type: "success"
                        });

                        resolve(response.data);
                    })
                    .catch(error => {
                        if (error.response) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            context.commit("setNotification", {
                                msg: "Unexpected error occured",
                                type: "error"
                            });
                        }

                        reject(error);
                    });
            });
        },

        setRole(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${context.state.config.base_url}/roles/set`,
                        payload.data
                    )
                    .then(response => {
                        let notification = {
                            msg: response.data.info,
                            type: "success"
                        };
                        context.commit("setNotification", notification);

                        resolve(response.data);
                    })
                    .catch(error => {
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }

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
                        `${context.state.config.base_url}/${payload.model}/create`,
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
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }

                        reject(error);
                    });
            });
        },
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(
                        `${context.state.config.base_url}/${payload.model}/${payload.id}`
                    )
                    .then(response => {
                        if (_.has(payload, "mutation")) {
                            context.commit(payload.mutation, response.data);
                        }

                        context.commit("setNotification", {
                            msg: response.data.info,
                            type: "success"
                        });

                        resolve(response.data);
                    })
                    .catch(error => {
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        },
        postRequest(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `${context.state.config.base_url}/${payload.url}`,
                        payload.data
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
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        },
        deleteRequest(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`${context.state.config.base_url}/${payload.url}`)
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
                        if (_.has(error, "response.data.errors")) {
                            context.commit("setNotification", {
                                msg: error.response.data.errors,
                                type: "error"
                            });
                        } else {
                            console.error(error);
                        }
                        reject(error);
                    });
            });
        }
    }
});

export const store = new Vuex.Store();
