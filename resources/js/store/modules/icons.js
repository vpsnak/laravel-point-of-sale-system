// initial state
const state = {
  dashboard: "dashboard",
  sale: "mdi-cart",
  orders: "mdi-buffer",
  order: "mdi-buffer",
  customers: "mdi-account-group",
  customer: "mdi-account",
  addresses: "mdi-account-card-details",
  address: "mdi-account-card-details",
  products: "mdi-package-variant",
  product: "mdi-package-variant",
  categories: "mdi-inbox-multiple",
  category: "mdi-inbox-multiple",
  giftCards: "mdi-wallet-giftcard",
  giftCard: "mdi-wallet-giftcard",
  coupons: "mdi-ticket-percent",
  coupon: "mdi-ticket-percent",
  cashRegisters: "mdi-cash-register",
  cashRegister: "mdi-cash-register",
  stores: "mdi-store",
  store: "mdi-store",
  storePickups: "mdi-storefront",
  storePickup: "mdi-storefront",
  companies: "mdi-domain",
  company: "mdi-domain",
  taxes: "mdi-sack-percent",
  tax: "mdi-sack-percent",
  reports: "mdi-file-document-box",
  report: "mdi-file-document-box",
  users: "mdi-account-multiple",
  user: "mdi-account-multiple",
  barcodeScan: "mdi-barcode-scan",
  addGiftCard: "mdi-credit-card-plus",
  cashCarry: "mdi-cart-arrow-right",
  storePickup: "mdi-storefront",
  delivery: "mdi-truck-delivery",
  edit: "mdi-pencil",
  plus: "mdi-plus",
  minus: "mdi-minus",
  view: "mdi-eye",
  hold: "mdi-pause",
  delete: "mdi-delete",
  recycle: "mdi-recycle",
  notes: "mdi-note-text",
  check: "mdi-check",
  customerSearch: "mdi-account-search"
};

// getters
const getters = {
  getIcon(model) {
    return state[model];
  }
};
