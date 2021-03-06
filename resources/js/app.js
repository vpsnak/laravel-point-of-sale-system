/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./includes");

import router from "./plugins/router";
import vuetify from "./plugins/vuetify"; // path to vuetify export
import BarcodeScanner from "simple-barcode-scanner";
import VueBarcode from "vue-barcode";
import Donut from "vue-css-donut-chart";
import Price from "./plugins/price";
import store from "./store/store";
import "vue-css-donut-chart/dist/vcdonut.css";
import PerfectScrollbar from "vue2-perfect-scrollbar";
import "vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css";

import {
  ValidationProvider,
  ValidationObserver,
} from "vee-validate/dist/vee-validate.full";

window.Vue = require("vue");

Vue.config.productionTip = false;

Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ValidationObserver", ValidationObserver);
Vue.component("barcode", VueBarcode);

Vue.use(Donut);
Vue.use(Price);
Vue.use(PerfectScrollbar);

const scanner = BarcodeScanner();

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context("./", true, /\.vue$/i);
files
  .keys()
  .map((key) =>
    Vue.component(key.split("/").pop().split(".")[0], files(key).default)
  );

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
  el: "#app",
  template: "<master />",
  router,
  store,
  vuetify,

  mounted() {
    scanner.on((code, event) => {
      event.preventDefault();
      this.$emit("barcodeScan", code);
    });
  },
  beforeDestroy() {
    this.$off("barcodeScan");
  },
});
