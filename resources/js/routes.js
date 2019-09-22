import Dashboard from "./components/pages/Dashboard";
import Sales from "./components/pages/Sales";
import Forms from "./components/pages/Forms";
import Orders from "./components/pages/Orders";
import Customers from "./components/pages/Customers";
import Products from "./components/pages/Products";
import Categories from "./components/pages/Categories";
import Stores from "./components/pages/Stores";
import Users from "./components/pages/Users";
import CTest from "./components/pages/CTest";


export default [
    {
        name: "dashboard",
        path: "/",
        component: Dashboard
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
];
