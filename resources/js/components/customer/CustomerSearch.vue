<template>
	<div class="d-flex">
		<v-autocomplete
			v-if="editable"
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
		<v-text-field v-else :value="getCustomerFullname(cartCustomer)" disabled prepend-icon="person"></v-text-field>
	</div>
</template>
<script>
export default {
	props: {
		keywordLength: Number,
		editable: Boolean | undefined
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
				return this.$store.state.cart.customer;
			},
			set(value) {
				this.$store.commit("cart/setCustomer", value);
			}
		}
	},
	methods: {
		getCustomerFullname(item) {
			if (item) {
				return item.first_name + " " + item.last_name;
			} else {
				return "Guest";
			}
		},
		searchCustomer(keyword) {
			this.loading = true;
			const payload = {
				model: "customers",
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
				if (keyword.length >= this.$props.keywordLength) {
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