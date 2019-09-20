<template>
	<v-card>
		<v-card-title>
			Customers
			<v-spacer></v-spacer>
			<v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
		</v-card-title>
		<v-data-table :headers="headers" :items="customerList" :search="search">
			<template slot="items" slot-scope="props">
				<td>{{ props.item.name }}</td>
				<td class="text-xs-right">{{ props.item.id }}</td>
				<td class="text-xs-right">{{ props.item.photo_url }}</td>
				<td class="text-xs-right">{{ props.item.sku }}</td>
				<td class="text-xs-right">{{ props.item.price_id }}</td>
				<td class="text-xs-right">{{ props.item.name }}</td>
			</template>
			<v-alert
				slot="no-results"
				:value="true"
				color="error"
				icon="warning"
			>Your search for "{{ search }}" found no results.</v-alert>
		</v-data-table>
	</v-card>
</template>


<script>
	export default {
		data() {
			return {
				loader: false,
				disableFilters: false,
				model: "customers",
				keyword: "",
				search: "",
				headers: [
					{
						text: "Customers",
						align: "left",
						sortable: false,
						value: "name"
					},
					{ text: "Id", value: "id" },
					{ text: "Sku", value: "sku" },
					{ text: "Photo url", value: "photo_url" },
					{ text: "Price_id", value: "price_id" }
				]
			};
		},
		mounted() {
			this.getAllCustomers();
		},
		computed: {
			customertList: {
				get() {
					return this.$store.state.customerList;
				},
				set(value) {
					this.$store.state.customerList = value;
				}
			}
		},
		methods: {
			applyFilter(filter) {},
			initiateLoadingSearchResults(loading) {
				if (loading) {
					this.loader = true;
					this.customerList = [];
				} else {
					this.loader = false;
				}
			},
			getAllCustomers() {
				this.initiateLoadingSearchResults(true);

				let payload = {
					model: "customers",
					mutation: "setCustomerList"
				};
				this.$store
					.dispatch("getAll", payload)
					.then(result => {
						this.initiateLoadingSearchResults(false);
					})
					.catch(error => {
						this.initiateLoadingSearchResults(false);
						console.log(error);
					});
				console.log(this.customerList);
			}
		}
	};
</script>