const state = {
    addressTable: [
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
    cashRegisterReportsTable: [
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
    cashRegisterTable: [
        { text: "#", value: "id" },
        { text: "Name", value: "name" },
        { text: "Store", value: "store.name" },
        { text: "Barcode", value: "barcode" },
        { text: "Is open", value: "is_open" },
        { text: "Operator", value: "" },
        { text: "Actions", value: "actions" }
    ],
    categoryTable: [
        { text: "#", value: "id" },
        { text: "Name", value: "name" },
        { text: "In product listing", value: "in_product_listing" },
        { text: "Actions", value: "actions" }
    ],
    companyTable: [
        { text: "#", value: "id" },
        { text: "Name", value: "name" },
        { text: "Tax number", value: "tax_number" },
        { text: "Actions", value: "actions" }
    ],
    couponTable: [
        { text: "#", value: "id" },
        { text: "Name", value: "name" },
        { text: "Code", value: "code" },
        { text: "Uses", value: "uses" },
        { text: "Discount Type", value: "discount.type" },
        { text: "Discount Amount", value: "discount.amount" },
        { text: "Actions", value: "actions" }
    ],
    customerTable: [
        { text: "#", value: "id" },
        { text: "First name", value: "first_name" },
        { text: "Last name", value: "last_name" },
        { text: "E-mail", value: "email" },
        { text: "No tax", value: "no_tax" },
        { text: "House Account", value: "house_account_status" },
        { text: "Actions", value: "actions" }
    ],
    giftCardTable: [
        { text: "#", value: "id" },
        { text: "Name", value: "name" },
        { text: "Code", value: "code" },
        { text: "Enabled", value: "enabled" },
        { text: "Amount", value: "amount" },
        { text: "Actions", value: "actions" }
    ],
    OrderTable: [
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
    productTable: [
        { text: "#", value: "id" },
        { text: "Image", value: "photo_url", shortable: false },
        { text: "Name", value: "name" },
        { text: "Sku", value: "sku" },
        { text: "Stock", value: "stock" },
        { text: "Price", value: "final_price" },
        { text: "Actions", value: "actions", shortable: false }
    ],
    storePickupTable: [
        { text: "#", value: "id" },
        { text: "Name", value: "name" },
        { text: "Street", value: "street" },
        { text: "Street 1", value: "street1" },
        { text: "Country ID", value: "country_id" },
        { text: "Region", value: "region.default_name" },
        { text: "Actions", value: "actions" }
    ],
    storeTable: [
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
    userTable: [
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
};

const getters = {
    getHeaders: state => {
        return state[model];
    }
};

export default {
    namespaced: true,
    state,
    getters
};
