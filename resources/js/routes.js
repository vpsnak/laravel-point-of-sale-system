import Dashboard from './components/pages/Dashboard'
import Login from './components/auth/Login'
import Logout from './components/auth/Logout'
import Sales from './components/pages/Sales'
import Forms from './components/pages/Forms'
import Orders from './components/pages/Orders'
import Customers from './components/pages/Customers'
import Products from './components/pages/Products'
import Categories from './components/pages/Categories'
import Stores from './components/pages/Stores'
import Users from './components/pages/Users'
import CTest from './components/pages/CTest'
import ListData from './components/pages/ListData'
import Taxes from './components/pages/Taxes'
import CashRegisters from './components/pages/CashRegisters'
import GiftCards from './components/pages/GiftCards'
import Coupons from './components/pages/Coupons'

export default [
    {
        path: "*",
        redirect: "/"
    },
    {
        name: "dashboard",
        path: "/",
        component: Dashboard,
        meta: {requiresAuth: false}
    },
    {
        name: "login",
        path: "/login",
        component: Login
    },
    {
        name: "logout",
        path: "/logout",
        component: Logout,
        meta: {requiresAuth: false}
    },
    {
        name: "sales",
        path: "/sales",
        component: Sales,
        meta: {requiresAuth: false}
    },
    {
        name: "Orders",
        path: "/orders",
        component: Orders,
        meta: {requiresAuth: false}
    },
    {
        name: "Customers",
        path: "/customers",
        component: Customers,
        meta: {requiresAuth: false}
    },
    {
        name: "Products",
        path: "/products",
        component: Products,
        meta: {requiresAuth: false}
    },
    {
        name: "Categories",
        path: "/categories",
        component: Categories,
        meta: {requiresAuth: false}
    },
    {
        name: "Stores",
        path: "/stores",
        component: Stores,
        meta: {requiresAuth: false}
    },
    {
        name: "Users",
        path: "/users",
        component: Users,
        meta: {requiresAuth: false}
    },
    {
        name: "testc",
        path: "/chris",
        component: CTest,
        meta: {requiresAuth: false}
    },
    {
        name: "Forms",
        path: "/forms",
        component: Forms,
        meta: {requiresAuth: false}
    },
    {
        name: "ListData",
        path: "/list-data",
        component: ListData,
        meta: {requiresAuth: false}
    },
    {
        name: "Taxes",
        path: "/taxes",
        component: Taxes,
        meta: {requiresAuth: false}
    },
    {
        name: "CashRegisters",
        path: "/cash-registers",
        component: CashRegisters,
        meta: {requiresAuth: false}
    },
    {
        name: "GiftCards",
        path: "/gift-cards",
        component: GiftCards,
        meta: {requiresAuth: false}
    },
    {
        name: "Coupons",
        path: "/coupons",
        component: Coupons,
        meta: {requiresAuth: false}
    }
];
