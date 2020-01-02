import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
import { preset } from "vue-cli-plugin-vuetify-preset-rally/preset";

Vue.use(Vuetify);

const options = {
    theme: {
        dark: true
    },
    icons: {
        iconfont: "mdi"
    }
};

export default new Vuetify({ preset, ...options });
