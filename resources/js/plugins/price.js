import Dinero from "dinero.js";

const Price = {
  install(Vue, defaultCurrency) {
    Object.defineProperty(Vue.prototype, "$price", { value: Dinero });
    Dinero.defaultCurrency = defaultCurrency || "USD";
    Dinero.defaultAmount = 0;

    Vue.mixin({
      methods: {
        parsePrice(value) {
          if (_.isObjectLike(value)) {
            if (_.has(value, "amount")) {
              value.amount = Number.parseInt(value.amount);
              return Dinero(value);
            } else {
              return value;
            }
          } else if (!Number.isInteger(value)) {
            return Dinero(Number.parseInt(value));
          } else {
            return value;
          }
        }
      }
    });
  }
};

export default Price;
