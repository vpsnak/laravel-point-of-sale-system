import Vue from "vue";
import { ValidationProvider, ValidationObserver, extend } from "vee-validate";

Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ValidationObserver", ValidationObserver);
