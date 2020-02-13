export default {
  namespaced: true,

  state: {
    tax_percentage: 0,
    checkoutDialog: false,
    refund_loading: false,
    payment_loading: false,
    complete_order_loading: false,
    payments: [],

    isValid: false,
    isValidCheckout: false,
    discount_error: false,

    discountErrors: [],

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
        label: "None",
        value: null
      },
      {
        label: "Flat",
        value: "Flat"
      },
      {
        label: "Percentage",
        value: "Percentage"
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
    shipping_cost: null,
    cart_products: [],

    discount_type: "",
    discount_amount: 0,

    delivery: {
      store_pickup_id: null,
      address: null,
      date: null,
      time: null,
      location: null,
      occasion: 9
    },

    billing_address: null,

    order_id: null,
    order_status: null,
    order_total: 0,
    order_total_without_tax: 0,
    order_total_tax: 0,
    order_change: 0,
    order_remaining: 0
  },

  mutations: {
    setOrderId(state, value) {
      state.order_id = value;
    },
    setOrderStatus(state, value) {
      state.order_status = value;
    },
    setOrderTotal(state, value) {
      state.order_total = value;
    },
    setOrderTotalWithoutTax(state, value) {
      state.order_total_without_tax = value;
    },
    setOrderTotalTax(state, value) {
      state.order_total_tax = value;
    },
    setOrderChange(state, value) {
      state.order_change = value;
    },
    setOrderRemaining(state, value) {
      state.order_remaining = value;
    },
    setCompleteOrderLoading(state, value) {
      state.complete_order_loading = value;
    },
    setPaymentLoading(state, value) {
      state.payment_loading = value;
    },
    setRefundLoading(state, value) {
      state.refund_loading = value;
    },
    setTaxPercentage(state, value) {
      state.tax_percentage = value;
    },
    setCartDiscountType(state, value) {
      state.discount_type = value;
    },
    setCartDiscountAmount(state, value) {
      state.discount_amount = value;
    },
    setDiscountError(state, value) {
      state.discount_error = value;
    },
    setMethod(state, value) {
      state.method = value;
    },
    setMethodStep(state, value) {
      state.checkoutSteps[0] = { ...state.checkoutSteps[0], value };
    },
    setCheckoutDialog(state, value) {
      state.checkoutDialog = value;
    },
    setPaymentRefundedStatus(state, index) {
      state.payments[index].refunded = true;
    },
    setPaymentHistory(state, value) {
      if (Array.isArray(value)) {
        state.payments = value;
      } else {
        state.payments.push(value);
      }
    },
    setIsValidCheckout(state, value) {
      state.isValidCheckout = value;
    },
    isValidDiscount(state) {
      let result = true;

      if (state.discount_error) {
        result = false;
      }

      state.cart_products.forEach(product => {
        if (product.discount_error) {
          result = false;
        }
      });

      state.isValidCheckout = result;
    },
    setShippingCost(state, value) {
      state.delivery_cost = value;
    },
    addProduct(state, newProduct) {
      let index = _.findIndex(state.cart_products, product => {
        return product.id === newProduct.id;
      });

      if (index !== -1) {
        state.cart_products[index].qty++;
      } else {
        let clonedProduct = _.cloneDeep(newProduct);
        Vue.set(clonedProduct, "qty", 1);

        state.cart_products.push(clonedProduct);
      }
    },
    removeProduct(state, index) {
      state.cart_products.splice(index, 1);
    },
    increaseProductQty(state, target_product) {
      let index = _.findIndex(state.cart_products, product => {
        return product.id === target_product.id;
      });

      if (index != -1) {
        state.cart_products[index].qty++;
      }
    },
    decreaseProductQty(state, target_product) {
      let index = _.findIndex(state.cart_products, product => {
        return product.id === target_product.id;
      });

      if (index != -1 && state.cart_products[index].qty > 1) {
        state.cart_products[index].qty--;
      }
    },
    setCartProductData(state, payload) {
      const newData = {
        ...state.cart_products[payload.index],
        ...payload.data
      };

      state.cart_products[payload.index] = newData;
    },
    setCustomer(state, value) {
      if (_.isObjectLike(value)) {
        state.customer = value;
      } else {
        state.customer = null;
      }
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
      state.order_id = null;
      state.order_status = null;
      state.order_total = 0;
      state.order_total_without_tax = 0;
      state.order_total_tax = 0;
      state.order_remaining = 0;
      state.order_change = 0;

      state.payments = [];

      state.checkoutDialog = false;
      state.payment_loading = false;
      state.customer = null;

      state.cart_products = [];
      state.discount_type = "";
      state.discount_amount = 0;

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
        location: null,
        occasion: 9,
        pickup_point_id: null
      };
      state.billing_address = null;
      state.complete_order_loading = false;
    },
    resetShipping(state) {
      state.delivery = {
        address_id: null,
        date: null,
        time: null,
        location: null,
        occasion: 9,
        pickup_point_id: null
      };
      state.billing_address = null;

      state.currentCheckoutStep = 1;
      state.checkoutSteps.forEach(checkoutStep => {
        checkoutStep.completed = false;
      });
      state.checkoutSteps[0].name = "Cash & Carry";
      state.checkoutSteps[0].icon = "mdi-cart-arrow-right";
      state.checkoutSteps[0].color = "primary";
    }
  },
  actions: {
    mailReceipt(context, payload) {
      return new Promise((resolve, reject) => {
        axios
          .post(
            `${context.rootState.config.base_url}/mail-receipt/${context.state.order.id}`,
            payload
          )
          .then(response => {
            let notification = {
              msg: response.data.info,
              type: "success"
            };

            context.commit("setNotification", notification, {
              root: true
            });
            resolve(response);
          })
          .catch(error => {
            let notification = {
              msg: error.response.data.errors,
              type: "error"
            };
            context.commit("setNotification", notification, {
              root: true
            });
            reject(error);
          });
      });
    },
    saveGuestEmail(context, payload) {
      return new Promise((resolve, reject) => {
        axios
          .post(
            `${context.rootState.config.base_url}/guest-email/create`,
            payload
          )
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            let notification = {
              msg: error.response.data.errors,
              type: "error"
            };
            context.commit("setNotification", notification, {
              root: true
            });
            reject(error);
          });
      });
    },
    createReceipt(context, payload) {
      return new Promise((resolve, reject) => {
        axios
          .get(
            `${context.rootState.config.base_url}/receipts/create/${payload}`
          )
          .then(response => {
            resolve(response.data);
          })
          .catch(error => {
            if (error.response) {
              let notification = {
                msg: error.response.data.errors,
                type: "error"
              };
              context.commit("setNotification", notification, {
                root: true
              });
            } else {
              let notification = {
                msg: "Unexpected error occured",
                type: "error"
              };
              context.commit("setNotification", notification, {
                root: true
              });
            }
            reject(error);
          });
      });
    },
    submitOrder(context) {
      return new Promise((resolve, reject) => {
        let payload = {
          model: "orders",
          data: {
            products: context.state.cart_products,
            method: context.state.method,
            discount_type: context.state.discount_type,
            discount_amount: context.state.discount_amount,
            customer_id: context.state.customer
              ? context.state.customer.id
              : "",
            billing_address: context.state.billing_address,
            notes: context.state.notes
          }
        };

        if (context.state.method !== "retail") {
          payload.delivery = context.state.delivery;
        }

        if (context.state.method === "delivery") {
          payload.shipping_cost = context.state.shipping_cost;
        }

        context
          .dispatch("create", payload, { root: true })
          .then(response => {
            context.commit("setOrderId", response.order_id);
            context.commit("setOrderStatus", response.order_status);
            context.commit("setOrderTotal", response.order_total);
            context.commit(
              "setOrderTotalWithoutTax",
              response.order_total_without_tax
            );
            context.commit("setOrderTotalTax", response.order_total_tax);
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    completeStep(context) {
      context.commit("completeStep");
      context.commit("nextCheckoutStep");
    }
  }
};
