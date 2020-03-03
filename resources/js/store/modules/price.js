import Dinero from "dinero.js";

const getters = {
  parsePrice: state => price => {
    return Dinero(price);
  },
  displayPrice: state => price => {
    return Dinero(price).toFormat("$0,0.00");
  },
  displayPriceNoSign: state => price => {
    return Dinero(price).toFormat("0,0.00");
  },
  addPrice: state => (price, value) => {
    return Dinero(price).add(value * 100);
  },
  subtractPrice: state => (price, value) => {
    return Dinero(price).subtract(value * 100);
  },
  multiplyPrice: state => (price, value) => {
    return Dinero(price).multiply(_.toInteger(value));
  },
  percentagePrice: state => (price, value) => {
    return Dinero(price).percentage(value);
  }
};

export default {
  namespaced: true,
  getters
};
