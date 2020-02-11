// initial state
const state = {
    headers: {
        addresses: [
            { text: "#", value: "id" },
            { text: "First Name", value: "first_name" },
            { text: "Last Name", value: "last_name" },
            { text: "Street", value: "street" },
            { text: "Street 2", value: "street2" },
            { text: "City", value: "city" },
            { text: "Country ID", value: "country_id" },
            { text: "Region", value: "address_region.default_name" },
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
            { text: "Store", value: "store.name" },
            { text: "Barcode", value: "barcode" },
            { text: "Is open", value: "is_open" },
            { text: "Operator", value: "" },
            { text: "Actions", value: "actions" }
        ],
        categories: [
            { text: "#", value: "id" },
            { text: "Name", value: "name" },
            { text: "In product listing", value: "in_product_listing" },
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
        giftCards: [
            { text: "#", value: "id" },
            { text: "Name", value: "name" },
            { text: "Code", value: "code" },
            { text: "Enabled", value: "enabled" },
            { text: "Amount", value: "amount" },
            { text: "Actions", value: "actions" }
        ],
        orders: [
            { text: "#", value: "id" },
            { text: "Customer", value: "customer" },
            { text: "Store", value: "store.name" },
            { text: "Status", value: "status" },
            { text: "Total", value: "total" },
            { text: "Total paid", value: "total_paid" },
            { text: "Created by", value: "created_by.name" },
            { text: "Created at", value: "created_at" },
            { text: "Actions", value: "actions" }
        ],
        products: [
            { text: "#", value: "id" },
            { text: "Image", value: "photo_url", shortable: false },
            { text: "Name", value: "name" },
            { text: "Sku", value: "sku" },
            { text: "Stock", value: "stock" },
            { text: "Price", value: "final_price" },
            { text: "Actions", value: "actions", shortable: false }
        ],
        storePickups: [
            { text: "#", value: "id" },
            { text: "Name", value: "name" },
            { text: "Street", value: "street" },
            { text: "Street 1", value: "street1" },
            { text: "Country ID", value: "country_id" },
            { text: "Region", value: "region.default_name" },
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
            { text: "Created_at", value: "created_at" },
            { text: "Updated_at", value: "updated_at" },
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
    },
    data_table: {
        rows: [],
        icon: "",
        title: "",
        headers: [],
        model: "",
        btnTxt: "",
        newForm: "",
        disableNewBtn: false,
        loading: false
    }
};

// mutations
const mutations = {
    resetDataTable(state) {
        state.data_table.rows = [];
        state.data_table.icon = "";
        state.data_table.title = "";
        state.data_table.headers = [];
        state.data_table.model = "";
        state.data_table.btnTxt = "";
        state.data_table.newForm = "";
        state.data_table.disableNewBtn = false;
        state.data_table.loading = false;
    },
    setDataTable(state, value) {
        state.data_table = { ...state.data_table, ...value };
    },
    setRows(state, value) {
        state.data_table.rows = value;
    },
    deleteRow(state, value) {
        state.data_table.rows = _.filter(state.data_table.rows, row => {
            return row.id !== value;
        });
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

const actions = {
    deleteRow(context, payload) {
        context.commit("setLoading", true);
        context
            .dispatch("deleteRequest", payload, { root: true })
            .then(result => {
                context.commit("setLoading", false);
                if (result === 1) {
                    context.commit("deleteRow", payload.data.id);
                }
            })
            .catch(error => {
                context.commit("setLoading", false);
            });
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    getters,
    actions
};
