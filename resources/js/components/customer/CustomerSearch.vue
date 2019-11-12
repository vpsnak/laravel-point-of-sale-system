<template>
	<div>
		<v-row align="center" justify="center">
			<v-combobox
				:no-filter="true"
				v-if="editable"
				v-model="cartCustomer"
				clearable
				dense
				:items="results"
				:loading="loading"
				:search-input.sync="search"
				color="white"
				hide-no-data
				hide-selected
				:item-text="getCustomerFullname"
				label="Select customer"
				placeholder="Start typing to Search"
				prepend-icon="mdi-account-search"
				return-object
			></v-combobox>
			<v-text-field v-else :value="getCustomerFullname(cartCustomer)" disabled prepend-icon="person"></v-text-field>
			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn @click="showCreateDialog = true" v-on="on" icon :disabled="!editable">
						<v-icon>mdi-plus</v-icon>
					</v-btn>
				</template>
				<span>Add a customer</span>
			</v-tooltip>
			<v-btn v-if="cartCustomerComment" @click="showCustomerComments = true" text icon color="red">
				<v-icon>mdi-comment</v-icon>
			</v-btn>
			<interactiveDialog
				v-if="showCreateDialog"
				:show="showCreateDialog"
				:model="{}"
				component="customerForm"
				title="Add a Customer"
				@action="result"
				cancelBtnTxt="Close"
				persistent
				titleCloseBtn
			></interactiveDialog>
			<interactiveDialog
				v-if="showCustomerComments"
				:show="showCustomerComments"
				:model="cartCustomer"
				component="customerComment"
				title="Customer comments"
				@action="result"
				cancelBtnTxt="Close"
				persistent
				titleCloseBtn
			></interactiveDialog>
		</v-row>
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
			showCustomerComments: false,
			showCreateDialog: false,
			customers: []
		};
	},
	computed: {
		results: {
			get() {
				return this.customers;
			},
			set(value) {
				this.customers = value;
			}
		},
		cartCustomer: {
			get() {
				return this.$store.state.cart.customer;
			},
			set(value) {
				if (value !== this.cartCustomer) {
					this.$store.commit("cart/resetShipping", value);
				}
				this.$store.commit("cart/setCustomer", value);
			}
		},
		cartCustomerComment: {
			get() {
				if (this.cartCustomer) {
					return this.cartCustomer.comment;
				} else return null;
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
					this.results = result;
				})
				.finally(() => (this.loading = false));
		},
		result(event) {
			this.showCreateDialog = false;
			this.showCustomerComments = false;
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
		}
	}
};
</script>