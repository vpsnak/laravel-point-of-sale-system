import Dinero from "dinero.js";

const Price = {
  install(Vue, defaultCurrency) {
    Object.defineProperty(Vue.prototype, "$price", { value: Dinero });
    Dinero.defaultCurrency = defaultCurrency || "USD";
    Dinero.defaultAmount = 0;

    Vue.mixin({
      methods: {
        calcDiscount(price, discount) {
          if (discount && _.has(discount, "price") && _.has(discount, "type")) {
            switch (_.lowerCase(discount.type)) {
              case "flat":
                return this.subtractPrice(price, discount.amount);
              case "percentage":
                return this.percentagePrice(price, discount.amount);
              default:
                return this.parsePrice(price);
            }
          } else {
            return this.parsePrice(price);
          }
        }
      }
    });
  }
};

export default Price;
