export default {
    namespaced: true,
    state: {
        toggle: true,

        adminItems: [
            {
                id: 1,
                title: "Dashboard",
                icon: "dashboard",
                to: "/"
            },
            {
                id: 2,
                title: "Sales",
                icon: "mdi-cart",
                to: "sales"
            },
            {
                id: 3,
                title: "Orders",
                icon: "mdi-buffer",
                to: "orders"
            },
            {
                id: 4,
                title: "Customers",
                icon: "mdi-account-group",
                to: "customers"
            },
            {
                id: 5,
                title: "Addresses",
                icon: "mdi-account-card-details",
                to: "addresses"
            },
            {
                id: 6,
                title: "Products",
                icon: "mdi-package-variant",
                to: "products"
            },
            {
                id: 7,
                title: "Categories",
                icon: "mdi-inbox-multiple",
                to: "categories"
            },
            {
                id: 8,
                title: "Gift Cards",
                icon: "mdi-wallet-giftcard",
                to: "gift-cards"
            },
            {
                id: 9,
                title: "Coupons",
                icon: "mdi-ticket-percent",
                to: "coupons"
            },
            {
                id: 10,
                title: "Cash registers",
                icon: "mdi-cash-register",
                to: "cash-registers"
            },
            {
                id: 11,
                title: "Stores",
                icon: "mdi-store",
                to: "stores"
            },
            {
                id: 12,
                title: "Store pickups",
                icon: "mdi-storefront",
                to: "store-pickups"
            },
            {
                id: 13,
                title: "Companies",
                icon: "mdi-domain",
                to: "companies"
            },
            {
                id: 14,
                title: "Taxes",
                icon: "mdi-sack-percent",
                to: "taxes"
            },
            {
                id: 15,
                title: "Reports",
                icon: "mdi-file-document-box",
                to: "reports"
            },
            {
                id: 16,
                title: "Users",
                icon: "mdi-account-multiple",
                to: "users"
            }
        ],
        managerItems: [
            {
                id: 1,
                title: "Dashboard",
                icon: "dashboard",
                to: "/"
            },
            {
                id: 2,
                title: "Sales",
                icon: "mdi-cart",
                to: "sales"
            },
            {
                id: 3,
                title: "Orders",
                icon: "mdi-buffer",
                to: "orders"
            },
            {
                id: 4,
                title: "Customers",
                icon: "mdi-account-group",
                to: "customers"
            },
            {
                id: 5,
                title: "Products",
                icon: "mdi-package-variant",
                to: "products"
            },
            {
                id: 6,
                title: "Categories",
                icon: "mdi-inbox-multiple",
                to: "categories"
            },
            {
                id: 7,
                title: "Reports",
                icon: "mdi-file-document-box",
                to: "reports"
            }
        ],
        cashierItems: [
            {
                id: 1,
                title: "Dashboard",
                icon: "dashboard",
                to: "/"
            },
            {
                id: 2,
                title: "Sales",
                icon: "mdi-cart",
                to: "sales"
            },
            {
                id: 3,
                title: "Orders",
                icon: "mdi-buffer",
                to: "orders"
            },
            {
                id: 4,
                title: "Customers",
                icon: "mdi-account-group",
                to: "customers"
            },
            {
                id: 5,
                title: "Products",
                icon: "mdi-package-variant",
                to: "products"
            }
        ]
    },

    mutations: {
        toggleMenu(state, value) {
            state.toggle = value;
        }
    }
};
