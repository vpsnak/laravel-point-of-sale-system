import Dinero from "dinero.js";

newPrice => amount => currency => {
  return Dinero({
    currency: currency || "USD",
    amount: amount || 0
  });
};

parsePrice => price => {
  return Dinero(price);
};

displayPrice => price => {
  return this.parsePrice(price).toFormat("$0,0.00");
};
displayPriceNoSign => price => {
  return this.parsePrice(price).toFormat("0,0.00");
};
addPrice => (price, value) => {
  return this.parsePrice(price).add(value);
};
subtractPrice => (price, value) => {
  return this.parsePrice(price).subtract(value);
};
multiplyPrice => (price, multi) => {
  return this.parsePrice(price).multiply(_.toInteger(multi));
};
percentagePrice => (price, value) => {
  return this.parsePrice(price).percentage(value);
};
calcTax => price => {
  return this.parsePrice(price).percentage(8.875);
};
calcDiscount => price => discount => {
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
    return this.parsePrice(price);
  }
};
