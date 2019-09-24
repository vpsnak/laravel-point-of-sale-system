<template>
	<div class="d-flex">
		<v-autocomplete
			v-model="cartCustomer"
			clearable
			dense
			:items="customers"
			:loading="loading"
			:search-input.sync="search"
			color="white"
			hide-no-data
			hide-selected
			:item-text="getCustomerFullname"
			label="Select customer"
			placeholder="Start typing to Search"
			prepend-icon="mdi-database-search"
			return-object
		></v-autocomplete>
	</div>
</template>

<script>
export default {
	props: {
		keywordLength: Number
	},

	data() {
		return {
			loading: false,
			search: null,
			customers: undefined
		};
	},
	computed: {
		cartCustomer: {
			get() {
				return this.$store.state.cartCustomer;
			},
			set(value) {
				this.$store.state.cartCustomer = value;
			}
		}
	},
	methods: {
		getCustomerFullname(item) {
			return item.first_name + " " + item.last_name;
		},
		searchCustomer(keyword) {
			this.loading = true;
			const payload = {
				model: "customers",
				mutation: "setCustomerList",
				keyword: keyword
			};

			this.$store
				.dispatch("search", payload)
				.then(result => {
					this.customers = result;
				})
				.catch(error => {
					console.log(error);
				})
				.finally(() => (this.loading = false));
		}
	},
	watch: {
		search(keyword) {
			if (keyword) {
				if (keyword.length > this.$props.keywordLength) {
					if (this.loading) {
						return;
					} else {
						this.searchCustomer(keyword);
						return;
					}
				}
			}
			this.cartCustomer = undefined;
			return;
		}
	}
};
</script>