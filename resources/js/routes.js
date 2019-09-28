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


export default [
    {
        name: "dashboard",
        path: "/",
        component: Dashboard
    },
    {
        name: 'login',
        path: '/login',
        component: Login
    },
    {
        name: 'logout',
        path: '/logout',
        component: Logout
    },
    {
        name: "sales",
        path: "/sales",
        component: Sales
    },
    {
        name: "Orders",
        path: "/orders",
        component: Orders
    },
    {
        name: "Customers",
        path: "/customers",
        component: Customers
    },
    {
        name: "Products",
        path: "/products",
        component: Products
    },
    {
        name: "Categories",
        path: "/categories",
        component: Categories
    },
    {
        name: "Stores",
        path: "/stores",
        component: Stores
    },
    {
        name: "Users",
        path: "/users",
        component: Users
    },
    {
        name: "testc",
        path: "/chris",
        component: CTest
    },
    {
        name: "Forms",
        path: "/forms",
        component: Forms
    },
    {
        name: "ListData",
        path: "/list-data",
        component: ListData
    },
    {
        name: "Taxes",
        path: "/taxes",
        component: Taxes
    }
];
