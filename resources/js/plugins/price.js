import Dinero from "dinero.js";

const Price = {
  install(Vue, defaultCurrency) {
    Object.defineProperty(Vue.prototype, "$price", { value: Dinero });
    Dinero.defaultCurrency = defaultCurrency || "USD";
    Dinero.defaultAmount = 0;
  }
};

export default Price;
