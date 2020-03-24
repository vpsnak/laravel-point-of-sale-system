// initial state
const state = {
  data_table: {
    items: [],
    icon: "",
    title: "",
    headers: [],
    model: "",
    btnTxt: "",
    newForm: "",
    disableNewBtn: false,
    loading: false,
    filters: null,
    refreshBtn: true
  },
  headers: {
    addresses: [
      { text: "#", value: "id" },
      { text: "First Name", value: "first_name" },
      { text: "Last Name", value: "last_name" },
      { text: "Street", value: "street" },
      { text: "Street 2", value: "street2" },
      { text: "City", value: "city" },
      { text: "Country ID", value: "country_id" },
      { text: "Region", value: "region.name" },
      { text: "Postcode", value: "postcode" },
      { text: "Phone", value: "phone" },
      { text: "Actions", value: "actions" }
    ],
    cashRegisterReports: [
      { text: "#", value: "id" },
      { text: "Report name", value: "report_name" },
      { text: "Report type", value: "report_type" },
      { text: "Cash register ID", value: "cash_register_id" },
      { text: "Opening amount", value: "opening_amount" },
      { text: "Closing amount", value: "closing_amount" },
      { text: "Subtotal", value: "subtotal" },
      { text: "Tax", value: "tax" },
      { text: "Total", value: "total" },
      { text: "Actions", value: "actions" }
    ],
    cashRegisters: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Store", value: "store_name" },
      { text: "Barcode", value: "barcode" },
      { text: "Pos terminal IP", value: "pos_terminal_ip" },
      { text: "Pos terminal port", value: "pos_terminal_port" },
      { text: "Is open", value: "is_open" },
      { text: "Open session with", value: "open_session_user.name" },
      { text: "Active", value: "active" },
      { text: "Actions", value: "actions" }
    ],
    categories: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Enabled", value: "is_enabled" },
      { text: "Actions", value: "actions" }
    ],
    companies: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Tax number", value: "tax_number" },
      { text: "Actions", value: "actions" }
    ],
    coupons: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Code", value: "code" },
      { text: "Uses", value: "uses" },
      { text: "Discount Type", value: "discount.type" },
      { text: "Discount Amount", value: "discount.amount" },
      { text: "Actions", value: "actions" }
    ],
    customers: [
      { text: "#", value: "id" },
      { text: "First name", value: "first_name" },
      { text: "Last name", value: "last_name" },
      { text: "E-mail", value: "email" },
      { text: "No tax", value: "no_tax" },
      { text: "House Account", value: "house_account_status" },
      { text: "Actions", value: "actions" }
    ],
    giftcards: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Code", value: "code" },
      { text: "Price", value: "price" },
      { text: "Enabled at", value: "enabled_at" },
      { text: "Created by", value: "created_by.name" },
      { text: "Actions", value: "actions" }
    ],
    orders: [
      { text: "#", value: "id" },
      { text: "Customer", value: "customer" },
      { text: "Store", value: "store" },
      { text: "Type", value: "method" },
      { text: "Status", value: "status" },
      { text: "Total", value: "total_price" },
      { text: "Total income", value: "income_price" },
      { text: "Created by", value: "created_by" },
      { text: "Created at", value: "created_at" },
      { text: "Actions", value: "actions" }
    ],
    products: [
      { text: "#", value: "id" },
      { text: "Image", value: "photo_url", shortable: false },
      { text: "Name", value: "name" },
      { text: "Sku", value: "sku" },
      { text: "Stock", value: "stock" },
      { text: "Price", value: "price" },
      { text: "Actions", value: "actions", shortable: false }
    ],
    storePickups: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Phone", value: "phone" },
      { text: "Street", value: "street" },
      { text: "Street 2", value: "street2" },
      { text: "State", value: "region.name" },
      { text: "Country", value: "region.country.name" },
      { text: "Actions", value: "actions" }
    ],
    stores: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Phone", value: "phone" },
      { text: "Street", value: "street" },
      { text: "Postcode", value: "postcode" },
      { text: "City", value: "city" },
      { text: "Tax", value: "tax.name" },
      { text: "Company", value: "company.name" },
      { text: "Active", value: "active" },
      { text: "Created by", value: "created_by" },
      { text: "Actions", value: "actions" }
    ],
    taxes: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Percentage", value: "percentage" },
      { text: "Actions", value: "actions" }
    ],
    users: [
      { text: "#", value: "id" },
      { text: "Name", value: "name" },
      { text: "Email", value: "email" },
      { text: "Phone", value: "phone" },
      { text: "Role", value: "roles[0].name" },
      { text: "Active", value: "active" },
      { text: "Created at", value: "created_at" },
      { text: "Updated at", value: "updated_at" },
      { text: "Actions", value: "actions" }
    ],
    cards: [
      { text: "#", value: "id" },
      { text: "Title", value: "title" },
      { text: "Content", value: "content" },
      { text: "Created by", value: "created_by" },
      { text: "Type", value: "cardable_type" },
      { text: "Created at", value: "created_at" },
      { text: "Updated at", value: "updated_at" },
      { text: "Actions", value: "actions" }
    ],
    paymentHistory: [
      {
        text: "Payment ID",
        value: "#",
        sortable: false
      },
      {
        text: "Operator",
        value: "created_by.name",
        sortable: false
      },
      {
        text: "Date",
        value: "created_at",
        sortable: false
      },
      {
        text: "Type",
        value: "payment_type.name",
        sortable: false
      },
      {
        text: "Status",
        value: "status",
        sortable: false
      },
      {
        text: "Amount (USD)",
        value: "amount",
        sortable: false
      },
      {
        text: "Actions",
        value: "actions"
      }
    ]
  }
};

// mutations
const mutations = {
  resetDataTable(state) {
    state.data_table.items = [];
    state.data_table.icon = "";
    state.data_table.title = "";
    state.data_table.headers = [];
    state.data_table.model = "";
    state.data_table.btnTxt = "";
    state.data_table.newForm = "";
    state.data_table.disableNewBtn = false;
    state.data_table.loading = false;
    state.data_table.filters = false;
    state.data_table.refreshBtn = true;
  },
  setDataTable(state, value) {
    state.data_table = { ...state.data_table, ...value };
  },
  setItems(state, value) {
    state.data_table.items = value;
  },
  setLoading(state, value) {
    state.data_table.loading = value;
  }
};

const getters = {
  getHeaders: state => {
    return state.headers[_.camelCase(state.data_table.model)];
  }
};

export default {
  namespaced: true,
  state,
  mutations,
  getters
};
