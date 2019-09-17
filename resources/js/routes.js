import Dashboard from "./components/pages/Dashboard";
import Sales from "./components/pages/Sales";
import NTest from "./components/pages/NTest";
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
        name: "testn",
        path: "/nikos",
        component: NTest
    },
    {
        name: "testc",
        path: "/chris",
        component: CTest
    }
];
