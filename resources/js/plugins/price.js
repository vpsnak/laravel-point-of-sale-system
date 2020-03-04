import Dinero from "dinero.js";

const Price = {
  install(Vue, options) {
    Vue.mixin({
      methods: {
        newPrice(amount, currency) {
          console.log("new price");
          console.log(amount);
          if (amount) {
            amount = Number.parseInt(amount);
          }

          return Dinero({
            amount: amount || 0,
            currency: currency || "USD"
          });
        },
        parsePrice(price) {
          if (_.has(price, "amount")) {
            price.amount = Number.parseInt(price.amount);
          }
          return Dinero(price);
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
          return this.parsePrice(price).percentage(value);
        },
        calcTax(price) {
          return this.parsePrice(price).percentage(8.875);
        },
        calcDiscount(price, discount) {
          if (_.has(discount, "price") && _.has(discount, "type")) {
            switch (_.lowerCase(discount.type)) {
              case "flat":
                return this.subtractPrice(price, discount.amount);
              case "percentage":
                return this.percentagePrice(price, discount.amount);
              default:
                return this.parsePrice(price);
            }
          } else {
            console.log("else");

            console.log(this.parsePrice(price).toFormat("$0,0.00"));
            return this.parsePrice(price);
          }
        }
      }
    });
  }
};

export default Price;
