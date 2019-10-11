<template>
	<v-navigation-drawer clipped app overflow :mini-variant="mini">
		<v-list>
			<v-list-item v-for="menuItem in menuItems" :key="menuItem.id" :to="menuItem.to">
				<v-list-item-icon>
					<v-icon>{{ menuItem.icon }}</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>{{ menuItem.title }}</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-divider dark class="my-4"></v-divider>
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
					icon: "shopping_cart",
					to: "sales"
				},
				{
					id: 3,
					title: "Orders",
					icon: "fab fa-buffer",
					to: "orders"
				},
				{
					id: 4,
					title: "Customers",
					icon: "fas fa-user-friends",
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
					icon: "fas fa-boxes",
					to: "categories"
				},
				{
					id: 7,
					title: "Stores",
					icon: "fas fa-store-alt",
					to: "stores"
				},
				{
					id: 8,
					title: "Taxes",
					icon: "fas fa-funnel-dollar",
					to: "taxes"
				},
				{
					id: 9,
					title: "Cash registers",
					icon: "fas fa-cash-register",
					to: "cash-registers"
				},
				{
					id: 10,
					title: "Gift Cards",
					icon: "fas fa-gifts",
					to: "gift-cards"
				},
				{
					id: 11,
					title: "Coupons",
					icon: "fas fa-ticket-alt",
					to: "coupons"
				},
				{
					id: 12,
					title: "Users",
					icon: "fas fa-users",
					to: "users"
				},
				{
					id: 13,
					title: "Forms",
					icon: "fas fa-cat",
					to: "forms"
				}
				// {
				// 	id: 14,
				// 	title: "List Data",
				// 	icon: "fas fa-female",
				// 	to: "list-data"
				// },
				// {
				// 	id: 15,
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
