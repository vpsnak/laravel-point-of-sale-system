import Vue from "vue";
import Vuetify from "vuetify/lib";

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        dark: false
    },
    icons: {
        iconfont: "mdi" | "fa" | "fab" | "fas"
    }
});

export const vuetify = new Vuetify({});
