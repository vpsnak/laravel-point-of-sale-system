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
              return Dinero({ amount: value.amount });
            } else {
              return Dinero(value);
            }
          } else if (!Number.isInteger(value)) {
            return Dinero({ amount: Number.parseInt(value) });
          } else if (!value) {
            return Dinero();
          } else {
            return Dinero({ amount: value });
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
              if (this.parsePrice(amount).greaterThan(this.parsePrice(0))) {
                return true;
              }
            }
          }
          return false;
        },
        calcProductDiscount(product) {
          if (this.productHasDiscount(product)) {
            const price = this.parsePrice(product.price).multiply(product.qty);
            switch (product.discount.type) {
              case "flat":
                return price.subtract(this.parsePrice(product.discount));
              case "percentage":
                return price.subtract(
                  price.percentage(product.discount.amount)
                );
              default:
                return this.parsePrice(product.price);
            }
          } else {
            return this.parsePrice(product.price);
          }
        }
      }
    });
  }
};

export default Price;
