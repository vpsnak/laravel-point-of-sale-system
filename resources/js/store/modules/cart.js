export default {
  namespaced: true,

  state: {
    checkout_loading: false,

    tax_percentage: 0,
    checkoutDialog: false,
    transactions: [],

    is_valid: false,
    isValidCheckout: false,
    discount_error: false,

    productMap: [],

    locations: [
      { id: 1, label: "Funeral Home" },
      { id: 2, label: "Church" },
      { id: 3, label: "Reception Hall" },
      { id: 4, label: "Hospital" },
      { id: 5, label: "Business" },
      { id: 6, label: "Home" },
      { id: 7, label: "Apartment" },
      { id: 8, label: "Hotel" },
      { id: 9, label: "International City" },
      { id: 10, label: "School" },
      { id: 11, label: "Other" },
      { id: 12, label: "Pick Up or Will Call" }
    ],

    occasions: [
      { id: 1, label: "Wedding" },
      { id: 2, label: "Birthday" },
      { id: 3, label: "Holiday" },
      { id: 4, label: "Anniversary" },
      { id: 5, label: "Business" },
      { id: 6, label: "New Baby" },
      { id: 7, label: "Get Well" },
      { id: 8, label: "Funeral" },
      { id: 9, label: "Other" }
    ],

    discountTypes: [
      {
        text: "None",
        value: "none"
      },
      {
        text: "Flat",
        value: "flat"
      },
      {
        text: "Percentage",
        value: "percentage"
      }
    ],

    checkoutSteps: [
      {
        id: 1,
        name: "Cash & Carry",
        icon: "mdi-cart-arrow-right",
        color: "primary",
        component: "shipping",
        completed: true
      },
      {
        id: 2,
        name: "Payment",
        icon: "mdi-cash-register",
        color: "",
        component: "paymentStep",
        completed: false
      },
      {
        id: 3,
        name: "Order Completed",
        icon: "check_circle",
        color: "",
        component: "completion",
        completed: false
      }
    ],
    currentCheckoutStep: 2,

    customer: null,
    method: "retail",
    delivery_fees_price: { amount: 0 },
    cart_products: [],

    order_discount: { type: "none", amount: null },

    delivery: {
      store_pickup_id: null,
      address_id: null,
      date: null,
      time: null,
      occasion: 9
    },

    billing_address_id: null,

    order_id: null,
    order_store: null,
    order_status: null,
    order_mas_order: null,
    order_total_price: { amount: 0 },
    order_mdse_price: { amount: 0 },
    order_tax_price: { amount: 0 },
    order_change_price: { amount: 0 },
    order_remaining_price: { amount: 0 },
    order_notes: null,
    order_billing_address: null,
    order_delivery_address: null,
    order_delivery_store_pickup: null,
    order_delivery_store_pickup: null,
    order_timestamp: null,
    order_created_by: null,

    order_page_actions: false
  },

  mutations: {
    setPaymentRefundedStatus(state, index) {
      state.transactions[index].payment.is_refundable = false;
    },
    setCheckoutLoading(state, value) {
      state.checkout_loading = value;
    },
    setOrderPageActions(state, value) {
      state.order_page_actions = value;
    },
    setReorder(state, items) {
      const strippedItems = items.map(({ discount, ...attrs }) => attrs);
      state.cart_products = strippedItems;
    },
    setCartProduct(state, payload) {
      state.cart_products[payload.index] = payload.value;
    },
    setOrderStore(state, value) {
      state.order_store = value;
    },
    setOrderCreatedBy(state, value) {
      state.order_created_by = value;
    },
    setOrderTimestamp(state, value) {
      state.order_timestamp = value;
    },
    setDeliveryStorePickup(state, value) {
      state.order_delivery_store_pickup = value;
    },
    setBillingAddress(state, value) {
      state.order_billing_address = value;
    },
    setDeliveryAddress(state, value) {
      state.order_delivery_address = value;
    },
    setOrderDelivery(state, value) {
      state.delivery = value;
    },
    setOrderNotes(state, value) {
      state.order_notes = value;
    },
    setDeliveryStorePickupId(state, value) {
      state.delivery.store_pickup_id = value;
    },
    setBillingAddressId(state, value) {
      state.billing_address_id = value;
    },
    setDeliveryAddressId(state, value) {
      state.delivery.address_id = value;
    },
    setDeliveryDate(state, value) {
      state.delivery.date = value;
    },
    setDeliveryTime(state, value) {
      state.delivery.time = value;
    },
    setDeliveryOccasion(state, value) {
      state.delivery.occasion = value;
    },
    setOrderId(state, value) {
      // patch, shouldn't be really needed
      state.order_id = Number.parseInt(value);
    },
    setOrderStatus(state, value) {
      state.order_status = value;
    },
    setOrderMasOrderStatus(state, value) {
      state.order_mas_order = value;
    },
    setOrderTotalPrice(state, value) {
      state.order_total_price = value;
    },
    setOrderIncomePrice(state, value) {
      state.order_income_price = value;
    },
    setOrderTaxPrice(state, value) {
      state.order_tax_price = value;
    },
    setOrderMdsePrice(state, value) {
      state.order_mdse_price = value;
    },
    setOrderChangePrice(state, value) {
      state.order_change_price = value;
    },
    setOrderRemainingPrice(state, value) {
      state.order_remaining_price = value;
    },
    setTaxPercentage(state, value) {
      state.tax_percentage = value;
    },
    setCartDiscount(state, value) {
      if (value.type) {
        state.order_discount.type = value.type;
      }

      state.order_discount.amount = value.amount;
    },
    setDiscountError(state, value) {
      state.discount_error = value;
    },
    setMethod(state, value) {
      state.method = value;
    },
    setMethodStep(state, value) {
      state.checkoutSteps[0].name = value.name;
      state.checkoutSteps[0].icon = value.icon;
      state.checkoutSteps[0].color = value.color;
    },
    setCheckoutDialog(state, value) {
      state.checkoutDialog = value;
    },
    setTransactions(state, value) {
      if (Array.isArray(value)) {
        state.transactions = value;
      } else {
        state.transactions.push(value);
      }
    },
    setIsValid(state, value) {
      state.is_valid = value;
    },
    setIsValidCheckout(state, value) {
      state.isValidCheckout = value;
    },
    isValidDiscount(state) {
      var result = true;

      if (state.discount_error) {
        result = false;
      }

      state.productMap.forEach(product => {
        if (product.discount_error) {
          result = false;
        }
      });

      state.isValidCheckout = result;
    },
    setDeliveryFeesPrice(state, value) {
      state.delivery_fees_price = value;
    },
    addProduct(state, newProduct) {
      let index = _.findIndex(state.cart_products, product => {
        return product.id === newProduct.id;
      });

      if (index !== -1) {
        state.cart_products[index].qty++;
      } else {
        const clonedProduct = _.cloneDeep(newProduct);
        Vue.set(clonedProduct, "qty", 1);

        state.cart_products.push(clonedProduct);
        state.productMap.push({
          id: clonedProduct.id,
          discount_error: false
        });
      }
    },
    removeProduct(state, index) {
      const productId = state.cart_products[index].id;
      const productMapindex = _.findIndex(state.productMap, {
        id: productId
      });

      state.cart_products.splice(index, 1);
      state.productMap.splice(productMapindex, 1);
    },
    increaseProductQty(state, target_product) {
      const index = _.findIndex(state.cart_products, product => {
        return product.id === target_product.id;
      });

      if (index !== -1) {
        state.cart_products[index].qty++;
      }
    },
    decreaseProductQty(state, target_product) {
      const index = _.findIndex(state.cart_products, product => {
        return product.id === target_product.id;
      });

      if (index !== -1 && state.cart_products[index].qty > 1) {
        state.cart_products[index].qty--;
      }
    },
    setCartProducts(state, value) {
      state.cart_products = value;
    },
    setCustomer(state, value) {
      if (_.isObjectLike(value)) {
        state.customer = value;
      } else {
        state.customer = null;
      }
    },
    setCustomerAddress(state, value) {
      state.customer.addresses = value;
    },
    completeStep(state) {
      let index = -1 + state.currentCheckoutStep;
      state.checkoutSteps[index].completed = true;
    },
    nextCheckoutStep(state) {
      state.currentCheckoutStep++;
    },
    previousStep(state) {
      if (state.currentCheckoutStep > 1) {
        state.currentCheckoutStep--;
      }
    },
    resetState(state) {
      state.checkout_loading = false;
      state.productMap = [];

      state.order_id = null;
      state.order_store_id = null;

      state.method = "retail";

      state.order_status = null;
      state.order_mas_order = null;

      state.order_total_price = { amount: 0 };
      state.order_tax_price = { amount: 0 };
      state.order_remaining_price = { amount: 0 };
      state.order_change_price = { amount: 0 };
      state.order_income_price = { amount: 0 };
      state.order_mdse_price = { amount: 0 };
      state.delivery_fees_price = { amount: 0 };
      state.transactions = [];
      state.order_notes = "";

      state.checkoutDialog = false;
      state.payment_loading = false;
      state.order_page_actions = false;
      state.customer = null;

      state.cart_products = [];
      state.order_discount = { type: "none", amount: null };

      state.checkoutSteps[0].name = "Cash & Carry";
      state.checkoutSteps[0].icon = "mdi-cart-arrow-right";
      state.checkoutSteps[0].color = "primary";
      state.currentCheckoutStep = 2;
      state.checkoutSteps.forEach(checkoutStep => {
        checkoutStep.completed = false;
      });

      state.delivery = {
        address_id: null,
        date: null,
        time: null,
        occasion: 9
      };
      state.billing_address_id = null;
    },
    resetDelivery(state, hard) {
      state.delivery = {
        address_id: null,
        date: null,
        time: null,
        occasion: 9
      };
      state.billing_address_id = null;

      state.currentCheckoutStep = 1;
      state.checkoutSteps.forEach(checkoutStep => {
        checkoutStep.completed = false;
      });

      if (hard) {
        state.method = "retail";
        state.checkoutSteps[0].name = "Cash & Carry";
        state.checkoutSteps[0].icon = "mdi-cart-arrow-right";
        state.checkoutSteps[0].color = "primary";
      }
    }
  },
  actions: {
    addProduct(context, payload) {
      context.commit("addProduct", payload);
      context.commit("isValidDiscount");
    },
    removeProduct(context, index) {
      context.commit("removeProduct", index);
      context.commit("isValidDiscount");
    },
    submitOrder(context, url) {
      return new Promise((resolve, reject) => {
        let payload = {
          method: "post",
          url: `orders/${url}`,
          data: {
            order_id: context.state.order_id,
            products: context.state.cart_products,
            method: context.state.method,
            discount: context.state.order_discount,
            customer_id: context.state.customer
              ? context.state.customer.id
              : "",
            billing_address_id: context.state.billing_address_id,
            notes: context.state.order_notes
          }
        };

        if (context.state.method !== "retail") {
          payload.data.delivery = context.state.delivery;
          payload.data.delivery_fees_price = context.state.delivery_fees_price;
        }

        context
          .dispatch("requests/request", payload, { root: true })
          .then(response => {
            if (url === "create") {
              context.commit("setOrderId", response.order_id);
              context.commit("setOrderStatus", response.order_status);
              context.commit("setOrderTotalPrice", response.order_total_price);
              context.commit("setOrderTaxPrice", response.order_tax_price);
            }
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    loadOrder(context, order) {
      return new Promise(resolve => {
        context.commit("resetState");

        context.commit("setOrderId", order.id);
        context.commit("setOrderStore", order.store);
        context.commit("setCartProducts", order.items);
        context.commit("setMethod", order.method);
        context.commit("setOrderStatus", order.status);
        context.commit("setOrderMasOrderStatus", order.mas_order);

        context.commit("setOrderTotalPrice", order.total_price);
        context.commit("setOrderIncomePrice", order.income_price);
        context.commit("setOrderMdsePrice", order.mdse_price);
        context.commit("setOrderTaxPrice", order.tax_price);
        context.commit("setOrderChangePrice", order.change);
        context.commit("setOrderRemainingPrice", order.remaining_price);
        context.commit("setTransactions", order.transactions);
        context.commit("setCartDiscount", order.discount);
        context.commit("setCustomer", order.customer);
        context.commit("setOrderNotes", order.notes);
        context.commit("setOrderCreatedBy", order.created_by);
        context.commit("setOrderTimestamp", {
          created_at: order.created_at,
          updated_at: order.updated_at
        });

        if (order.method !== "retail") {
          context.commit("setDeliveryFeesPrice", order.delivery_fees_price);
          context.commit("setDeliveryDate", order.delivery.date);
          context.commit("setDeliveryTime", order.delivery.time);

          if (order.method === "pickup") {
            context.commit(
              "setDeliveryStorePickup",
              order.delivery.store_pickup
            );
          } else if (order.method === "delivery") {
            context.commit("setBillingAddress", order.billing_address);
            context.commit("setDeliveryAddress", order.delivery.address);
            context.commit("setDeliveryOccasion", order.delivery.occasion);
          }
        }

        resolve(true);
      });
    },

    completeStep(context) {
      context.commit("completeStep");
      context.commit("nextCheckoutStep");
    }
  }
};
