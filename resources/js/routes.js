import Dashboard from "./components/pages/Dashboard";
import Login from "./components/auth/Login";
import Logout from "./components/auth/Logout";
import Sales from "./components/pages/Sales";
import Forms from "./components/pages/Forms";
import Orders from "./components/pages/Orders";
import Customers from "./components/pages/Customers";
import Products from "./components/pages/Products";
import Categories from "./components/pages/Categories";
import Stores from "./components/pages/Stores";
import Users from "./components/pages/Users";
import CTest from "./components/pages/CTest";
import ListData from "./components/pages/ListData";
import Taxes from "./components/pages/Taxes";
import CashRegisters from "./components/pages/CashRegisters";
import GiftCards from "./components/pages/GiftCards";
import Coupons from "./components/pages/Coupons";
import Addresses from "./components/pages/Addresses";
import StorePickups from "./components/pages/StorePickups";
import OpenCashRegister from "./components/pages/OpenCashRegister";

export default [
    {
        path: "*",
        redirect: "/",
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
        meta: { requiresAuth: true }
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
        name: "Orders",
        path: "/orders",
        component: Orders,
        meta: { requiresAuth: true }
    },
    {
        name: "Customers",
        path: "/customers",
        component: Customers,
        meta: { requiresAuth: true }
    },
    {
        name: "Products",
        path: "/products",
        component: Products,
        meta: { requiresAuth: true }
    },
    {
        name: "Categories",
        path: "/categories",
        component: Categories,
        meta: { requiresAuth: true }
    },
    {
        name: "Stores",
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
        name: "testc",
        path: "/chris",
        component: CTest,
        meta: { requiresAuth: true }
    },
    {
        name: "Forms",
        path: "/forms",
        component: Forms,
        meta: { requiresAuth: true }
    },
    {
        name: "ListData",
        path: "/list-data",
        component: ListData,
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
    }
];
