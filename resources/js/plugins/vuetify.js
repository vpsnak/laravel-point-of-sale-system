import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify)

const opts = {
    theme: {
        dark: true
    },
    icons: {
        iconfont: "mdi" | "fa" | "fab" | "fas"
    }
}

export default new Vuetify(opts)