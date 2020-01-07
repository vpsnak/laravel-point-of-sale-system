import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";

Vue.use(Vuetify);

const opts = {
    theme: {
        dark: true,
        themes: {
            light: {
                primary: "#1976D2",
                secondary: "#78909C",
                accent: "#82B1FF",
                error: "#FF5252",
                info: "#2196F3",
                success: "#4CAF50",
                warning: "#FB8C00"
            },
            dark: {
                primary: "#2196F3",
                secondary: "#546E7A",
                accent: "#FF4081",
                error: "#FF5252",
                info: "#2196F3",
                success: "#4CAF50",
                warning: "#FB8C00"
            }
        }
    }
};

export default new Vuetify({ ...opts });
