import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";
import "es6-promise/auto";

Vue.use(Vuex);
Vue.use(axios);

export default new Vuex.Store({
    state: {
        baseUrl: "http://plantshed.test/api/",
        products: []
        // Current state of the application lies here.
    },
    getters: {
        // Compute derived state based on the current state. More like computed property.
    },
    mutations: {
        // Mutate the current state
    },
    actions: {
        search(context, payload) {
            console.log(payload);
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        this.state.baseUrl + payload.model + "/search",
                        payload
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
