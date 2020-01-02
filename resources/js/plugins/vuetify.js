import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
import { preset } from "vue-cli-plugin-vuetify-preset-reply/preset";

Vue.use(Vuetify);

const options = {
    breakpoint: { scrollbarWidth: 12 },
    theme: {
        dark: true
    },
    icons: {
        iconfont: "mdi"
    }
};

export default new Vuetify({ preset, ...options });
