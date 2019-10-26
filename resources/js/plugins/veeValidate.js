import Vue from "vue";
import { ValidationProvider, ValidationObserver, extend } from "vee-validate";

// // Add a rule.
// extend("secret", {
//     validate: value => value === "example",
//     message: "This is not the magic word"
// });

// Register it globally
Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ValidationObserver", ValidationObserver);
