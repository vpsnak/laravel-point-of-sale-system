<template>
	<v-navigation-drawer app overflow :mini-variant="mini">
		<v-list dense nav>
			<v-list-item v-for="menuItem in menuItems" :key="menuItem.id" :to="menuItem.to">
				<v-list-item-icon>
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
export default {
	data() {
		return {
			mini: false,
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
					title: "Forms",
					icon: "fab fa-python",
					to: "forms"
				},
				{
					id: 4,
					title: "Orders",
					icon: "fas fa-clone",
					to: "orders"
				},
				{
					id: 5,
					title: "Customers",
					icon: "fas fa-user-friends",
					to: "customers"
				},
				{
					id: 6,
					title: "Products",
					icon: "fas fa-coins",
					to: "products"
				},
				{
					id: 7,
					title: "Categories",
					icon: "fas fa-th-list",
					to: "categories"
				},
				{
					id: 8,
					title: "Stores",
					icon: "fas fa-store",
					to: "stores"
				},
				{
					id: 9,
					title: "Users",
					icon: "fas fa-users",
					to: "users"
				},
				{
					id: 10,
					title: "Chris",
					icon: "fa-cannabis",
					to: "chris"
				}
			]
		};
	},
	methods: {}
};
</script>