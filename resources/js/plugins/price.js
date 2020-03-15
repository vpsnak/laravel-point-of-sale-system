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
              return Dinero({ amount: 0 });
            }
          } else if (!value) {
            return Dinero({ amount: 0 });
          } else if (_.isInteger(value)) {
            return Dinero({ amount: value });
          } else {
            return Dinero({ amount: Number.parseInt(value) });
          }
        },
        productHasDiscount(product) {
          if (
            _.has(product, "discount") &&
            _.has(product.discount, "type") &&
            _.has(product.discount, "amount")
          ) {
            const type = product.discount.type;
            const amount = product.discount.amount;

            if (["flat", "percentage"].indexOf(type) !== -1) {
              if (!this.parsePrice(amount).isZero()) {
                return true;
              }
            }
          }
          return false;
        },
        calcProductDiscount(product) {
          const price = this.parsePrice(product.price).multiply(product.qty);
          if (this.productHasDiscount(product)) {
            switch (product.discount.type) {
              case "flat":
                return price.subtract(this.parsePrice(product.discount));
              case "percentage":
                const multiplier = Number.parseInt(product.discount.amount);
                if (multiplier > 0) {
                  return price.subtract(price.percentage(multiplier));
                } else {
                  return price;
                }
              default:
                return price;
            }
          } else {
            return price;
          }
        }
      }
    });
  }
};

export default Price;
