import Vue from "vue";
import Vuex from "vuex";
import "es6-promise/auto";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        // Current state of the application lies here.
    },
    getters: {
        // Compute derived state based on the current state. More like computed property.
    },
    mutations: {
        // Mutate the current state
    },
    actions: {
        // Get data from server and send that to mutations to mutate the current state
    }
});

export const store = new Vuex.Store({});
