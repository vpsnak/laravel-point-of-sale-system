// Auth
import TopMenu from "../components/menu/TopMenu";
import SideMenu from "../components/menu/SideMenu";
import Login from "../components/auth/Login";
import Logout from "../components/auth/Logout";

import LandingPage from "../components/blocks/LandingPage.vue";
import Dashboard from "../components/pages/Dashboard";
import Sales from "../components/pages/Sales";
import OpenCashRegister from "../components/pages/OpenCashRegister";

import Orders from "../components/order/table/OrderTable";
import OrderViewPage from "../components/order/pages/OrderViewPage";
import OrderEditOptionsPage from "../components/order/pages/OrderEditOptionsPage";
import Settings from "../components/settings/SettingsPage";
// data tables
import Customers from "../components/tables/CustomerTable";
import Products from "../components/tables/ProductTable";
import Categories from "../components/tables/CategoryTable";
import Stores from "../components/tables/StoreTable";
import Users from "../components/tables/UserTable";
import Taxes from "../components/tables/TaxTable";
import CashRegisters from "../components/tables/CashRegisterTable";
import GiftCards from "../components/tables/GiftCardTable";
import Coupons from "../components/tables/CouponTable";
import StorePickups from "../components/tables/StorePickupTable";
import Reports from "../components/tables/CashRegisterReportsTable";

export default [
  {
    path: "*",
    redirect: "/",
    meta: { requiresAuth: true }
  },
  {
    name: "landingPage",
    path: "/",
    component: LandingPage,
    meta: { requiresAuth: true }
  },
  {
    name: "dashboard",
    path: "/dashboard",
    components: {
      default: Dashboard,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
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
    components: {
      default: OpenCashRegister
    },
    meta: { requiresAuth: true }
  },
  {
    name: "sales",
    path: "/sales",
    components: {
      default: Sales,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true },
    props: {
      default: {
        showMethods: true,
        showCustomer: true,
        showActions: true
      }
    }
  },
  {
    name: "orders",
    path: "/orders",
    components: {
      default: Orders,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "viewOrderDetails",
    path: "/order/:id/details",
    component: OrderViewPage,
    meta: { requiresAuth: true }
  },
  {
    name: "editOrderItemsPage",
    path: "/order/:id/edit-items",
    component: Sales,
    meta: { requiresAuth: true },
    props: { showSave: true }
  },
  {
    name: "editOrderOptionsPage",
    path: "/order/:id/edit-options",
    component: OrderEditOptionsPage,
    meta: { requiresAuth: true }
  },
  {
    name: "customers",
    path: "/customers",
    components: {
      default: Customers,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "products",
    path: "/products",
    components: {
      default: Products,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "categories",
    path: "/categories",
    components: {
      default: Categories,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "stores",
    path: "/stores",
    components: {
      default: Stores,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "Users",
    path: "/users",
    components: {
      default: Users,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "StorePickups",
    path: "/store-pickups",
    components: {
      default: StorePickups,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "Taxes",
    path: "/taxes",
    components: {
      default: Taxes,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "CashRegisters",
    path: "/cash-registers",
    components: {
      default: CashRegisters,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "Reports",
    path: "/reports",
    components: {
      default: Reports,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "GiftCards",
    path: "/gift-cards",
    components: {
      default: GiftCards,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "Coupons",
    path: "/coupons",
    components: {
      default: Coupons,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  },
  {
    name: "Settings",
    path: "/settings",
    components: {
      default: Settings,
      side_menu: SideMenu,
      top_menu: TopMenu
    },
    meta: { requiresAuth: true }
  }
];
