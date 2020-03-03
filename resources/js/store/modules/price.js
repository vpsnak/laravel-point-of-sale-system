import Dinero from "dinero.js";

const state = {
  default_currency: 'USD'
},

const getters = {
  parsePrice: state => price => {
    return Dinero(price);
  },
  displayPrice: (state, getters) => price => {
    return getters.parsePrice(price).toFormat("$0,0.00");
  },
  displayPriceNoSign: (state, getters) => price => {
    return getters.parsePrice(price).toFormat("0,0.00");
  },
  addPrice: (state, getters) => (price, value) => {
    return getters.parsePrice(price).add(value * 100);
  },
  subtractPrice: (state, getters) => (price, value) => {
    return getters.parsePrice(price).subtract(value * 100);
  },
  multiplyPrice: (state, getters) => (price, multi) => {
    return getters.parsePrice(price).multiply(_.toInteger(multi));
  },
  percentagePrice: (state, getters) => (price, value) => {
    return getters.parsePrice(price).percentage(value);
  },

  calcDiscount: (state, getters) => (price, discount) => {
    if (_.has(discount, "price") && _.has(discount, "type")) {
      switch (_.lowerCase(discount.type)) {
        case "flat":
          return getters.subtractPrice(price, discount.amount);
        case "percentage":
          return getters.percentagePrice(price, discount.amount);
        default:
          return getters.parsePrice(price);
      }
    } else {
      return getters.parsePrice(price);
    }
  }
};

export default {
  namespaced: true,
  state,
  getters
};
