<template>
	<v-navigation-drawer clipped app overflow :mini-variant="mini">
		<v-list dense>
			<v-list-item v-for="menuItem in menuItems" :key="menuItem.id" :to="menuItem.to">
				<v-list-item-icon>
					<v-icon>{{ menuItem.icon }}</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>{{ menuItem.title }}</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-divider dark class="my-4"></v-divider>
			<v-subheader class="mt-5 grey--text text--darken-1">Designed and developed by WebO2</v-subheader>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
import { mapState } from "vuex";

export default {
	data() {
		return {
			menuItems: [
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
					title: "Stores",
					icon: "mdi-store",
					to: "stores"
				},
				{
					id: 8,
					title: "Store pickups",
					icon: "mdi-storefront",
					to: "store-pickups"
				},
				{
					id: 9,
					title: "Taxes",
					icon: "mdi-sack-percent",
					to: "taxes"
				},
				{
					id: 10,
					title: "Cash registers",
					icon: "mdi-cash-register",
					to: "cash-registers"
				},
				{
					id: 11,
					title: "Gift Cards",
					icon: "mdi-wallet-giftcard",
					to: "gift-cards"
				},
				{
					id: 12,
					title: "Coupons",
					icon: "mdi-ticket-percent",
					to: "coupons"
				},
				{
					id: 13,
					title: "Users",
					icon: "mdi-account-multiple",
					to: "users"
				},
				{
					id: 14,
					title: "Addresses",
					icon: "fas fa-address-card",
					to: "addresses"
				},
				{
					id: 15,
					title: "Forms",
					icon: "fas fa-cat",
					to: "forms"
				}
				// {
				// 	id: 16,
				// 	title: "List Data",
				// 	icon: "fas fa-female",
				// 	to: "list-data"
				// },
				// {
				// 	id: 17,
				// 	title: "Chris",
				// 	icon: "fa-cannabis",
				// 	to: "chris"
				// }
			]
		};
	},
	computed: {
		...mapState("topMenu", ["mini"])
	}
};
</script>
