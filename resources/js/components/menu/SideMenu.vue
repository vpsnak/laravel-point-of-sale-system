<template>
	<v-navigation-drawer app overflow :mini-variant="mini">
		<v-list nav>
			<v-list-item v-for="menuItem in menuItems" :key="menuItem.id" :to="menuItem.to">
				<v-list-item-icon small>
					<v-icon>{{ menuItem.icon }}</v-icon>
				</v-list-item-icon>

				<v-list-item-content>
					<v-list-item-title>{{ menuItem.title }}</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
	import { mapState } from "vuex";

	export default {
		data() {
			return {
				menuItems: [
					// {
					// 	id: 1,
					// 	title: "Dashboard",
					// 	icon: "dashboard",
					// 	to: "/"
					// },
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
						icon: "fas fa-box-open",
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
						title: "Users",
						icon: "fas fa-users",
						to: "users"
					},
					{
						id: 10,
						title: "Forms",
						icon: "fas fa-cat",
						to: "forms"
					},
					// {
					// 	id: 11,
					// 	title: "List Data",
					// 	icon: "fas fa-female",
					// 	to: "list-data"
					// },
					// {
					// 	id: 12,
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
