export default {
  namespaced: true,

  state: {
    tax_percentage: 0,
    checkoutDialog: false,
    refund_loading: false,
    payment_loading: false,
    complete_order_loading: false,
    payments: [],

    is_valid: false,
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
    delivery_fees_price: {
      amount: 0
    },
    cart_products: [],

    order_discount: { amount: 0 },

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
    order_total: 0,
    order_total_without_tax: 0,
    order_total_tax: 0,
    order_total_item_cost: 0,
    order_change: 0,
    order_remaining: 0,
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
    setOrderPageActions(state, value) {
      state.order_page_actions = value;
    },
    setReorder(state, items) {
      let strippedItems = items.map(({ discount, ...attrs }) => attrs);

      state.cart_products = strippedItems;
    },
    setCartProduct(state, payload) {
      state.cart_products[payload.index] = payload.value;
    },
    setOrderTotalItemCost(state, value) {
      state.order_total_item_cost = value;
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
      state.order_id = value;
    },
    setOrderStatus(state, value) {
      state.order_status = value;
    },
    setOrderMasOrderStatus(state, value) {
      state.order_mas_order = value;
    },
    setOrderTotal(state, value) {
      state.order_total = value;
    },
    setOrderTotalPaid(state, value) {
      state.order_total_paid = value;
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
    setCartDiscount(state, value) {
      state.order_discount = value;
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
    setPaymentRefundedStatus(state, index) {
      state.payments[index].refunded = true;
    },
    setPayments(state, value) {
      if (Array.isArray(value)) {
        state.payments = value;
      } else {
        state.payments.push(value);
      }
    },
    setIsValid(state, value) {
      state.is_valid = value;
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
        let clonedProduct = _.cloneDeep(newProduct);
        Vue.set(clonedProduct, "qty", 1);

        state.cart_products.push(clonedProduct);
      }
    },
    removeProduct(state, index) {
      state.cart_products.splice(index, 1);
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
      state.order_id = null;
      state.order_store_id = null;

      state.method = "retail";

      state.order_status = null;
      state.order_mas_order = null;

      state.order_total = 0;
      state.order_total_tax = 0;
      state.order_total_without_tax = 0;
      state.order_remaining = 0;
      state.order_change = 0;
      state.order_total_paid = 0;
      state.order_total_item_cost = 0;
      state.delivery_fees_price = {
        amount: 0
      };
      state.payments = [];
      state.order_notes = "";

      state.checkoutDialog = false;
      state.payment_loading = false;
      state.order_page_actions = false;
      state.customer = null;

      state.cart_products = [];
      state.order_discount = {
        amount: 0
      };

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
      state.complete_order_loading = false;
    },
    resetShipping(state, hard) {
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
    submitOrder(context, url) {
      return new Promise((resolve, reject) => {
        let payload = {
          method: "post",
          url: `orders/${url}`,
          data: {
            order_id: context.state.order_id || null,
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
              context.commit("setOrderTotal", response.order_total);
              context.commit(
                "setOrderTotalWithoutTax",
                response.order_total_without_tax
              );
              context.commit("setOrderTotalTax", response.order_total_tax);
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

        context.commit("setOrderTotal", order.total);
        context.commit("setOrderTotalPaid", order.total_paid);
        context.commit("setOrderTotalWithoutTax", order.total_without_tax);
        context.commit("setOrderTotalItemCost", order.total_item_cost);
        context.commit("setOrderTotalTax", order.total_tax);
        context.commit("setOrderChange", order.change);
        context.commit("setOrderRemaining", order.remaining);
        context.commit("setPayments", order.payments);
        context.commit("setCartDiscountType", order.discount_type);
        context.commit("setCartDiscountAmount", order.discount_amount);
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
