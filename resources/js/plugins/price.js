import Dinero from "dinero.js";

const Price = {
  install(Vue, options) {
    Vue.mixin({
      methods: {
        parsePrice(price) {
          if (price instanceof Dinero) {
            return price;
          } else {
            return Dinero(price);
          }
        },
        newPrice(amount, currency) {
          if (amount) {
            amount = Number.parseInt(amount);
          }
          const price = {
            amount: amount || 0,
            currency: currency || "USD"
          };
          return this.parsePrice(price);
        },
        displayPrice(price) {
          return this.parsePrice(price).toFormat("$0,0.00");
        },
        displayPriceNoSign(price) {
          return this.parsePrice(price).toFormat("0,0.00");
        },
        addPrice(price, value) {
          return this.parsePrice(price).add(value);
        },
        subtractPrice(price, value) {
          return this.parsePrice(price).subtract(value);
        },
        multiplyPrice(price, value) {
          return this.parsePrice(price).multiply(Number(value));
        },
        percentagePrice(price, value) {
          return this.parsePrice(price).percentage(Number(value));
        },
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
