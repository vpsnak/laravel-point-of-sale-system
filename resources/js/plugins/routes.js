import Dashboard from "../components/pages/Dashboard";
import Login from "../components/auth/Login";
import Logout from "../components/auth/Logout";
import Sales from "../components/pages/Sales";
import OpenCashRegister from "../components/pages/OpenCashRegister";

// data tables
import Orders from "../components/tables/OrderTable";
import Customers from "../components/tables/CustomerTable";
import Products from "../components/tables/ProductTable";
import Categories from "../components/tables/CategoryTable";
import Stores from "../components/tables/StoreTable";
import Users from "../components/tables/UserTable";

import Taxes from "../components/tables/TaxTable";
import CashRegisters from "../components/tables/CashRegisterTable";
import GiftCards from "../components/tables/GiftCardTable";
import Coupons from "../components/tables/CouponTable";
import Addresses from "../components/tables/AddressTable";
import StorePickups from "../components/tables/StorePickupTable";

import Reports from "../components/tables/CashRegisterReportsTable";
import Companies from "../components/tables/CompanyTable";

export default [
    {
        path: "*",
        redirect: "/",
        name: "*",
        meta: { requiresAuth: true }
    },
    {
        name: "dashboard",
        path: "/",
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        name: "login",
        path: "/login",
        component: Login,
        meta: { requiresAuth: false }
    },
    {
        name: "logout",
        path: "/logout",
        component: Logout,
        meta: { requiresAuth: false }
    },
    {
        name: "openCashRegister",
        path: "/open-cash-register",
        component: OpenCashRegister,
        meta: { requiresAuth: true }
    },
    {
        name: "sales",
        path: "/sales",
        component: Sales,
        meta: { requiresAuth: true }
    },
    {
        name: "orders",
        path: "/orders",
        component: Orders,
        meta: { requiresAuth: true }
    },
    {
        name: "customers",
        path: "/customers",
        component: Customers,
        meta: { requiresAuth: true }
    },
    {
        name: "products",
        path: "/products",
        component: Products,
        meta: { requiresAuth: true }
    },
    {
        name: "categories",
        path: "/categories",
        component: Categories,
        meta: { requiresAuth: true }
    },
    {
        name: "stores",
        path: "/stores",
        component: Stores,
        meta: { requiresAuth: true }
    },
    {
        name: "Users",
        path: "/users",
        component: Users,
        meta: { requiresAuth: true }
    },
    {
        name: "StorePickups",
        path: "/store-pickups",
        component: StorePickups,
        meta: { requiresAuth: true }
    },
    {
        name: "Taxes",
        path: "/taxes",
        component: Taxes,
        meta: { requiresAuth: true }
    },
    {
        name: "CashRegisters",
        path: "/cash-registers",
        component: CashRegisters,
        meta: { requiresAuth: true }
    },
    {
        name: "Reports",
        path: "/reports",
        component: Reports,
        meta: { requiresAuth: true }
    },
    {
        name: "GiftCards",
        path: "/gift-cards",
        component: GiftCards,
        meta: { requiresAuth: true }
    },
    {
        name: "Coupons",
        path: "/coupons",
        component: Coupons,
        meta: { requiresAuth: true }
    },
    {
        name: "Addresses",
        path: "/addresses",
        component: Addresses,
        meta: { requiresAuth: true }
    },
    {
        name: "Companies",
        path: "/companies",
        component: Companies,
        meta: { requiresAuth: true }
    }
];
