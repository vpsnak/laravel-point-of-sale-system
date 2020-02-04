<template>
	<v-card>
		<v-card-title>
			<v-img v-if="$props.icon">{{ $props.icon }}</v-img>
			{{ $props.title }}
			<v-spacer></v-spacer>
			<v-text-field
				:disabled="loading"
				:search="search"
				prepend-icon="search"
				hide-details
				label="Search"
				single-line
				v-model="keyword"
				append-icon="mdi-close"
				@click:append="keyword=null, searchAction=null, paginate($event)"
				@click:prepend="search"
				@keyup.enter="search"
			></v-text-field>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-btn
				:disabled="$props.tableBtnDisable || loading"
				color="primary"
				@click="createItemDialog"
			>{{ $props.tableBtnTitle }}</v-btn>
		</v-card-title>

		<v-data-table
			disable-sort
			dense
			:disable-filtering="true"
			:headers="$props.tableHeaders"
			:items="rows"
			:loading="loading"
			:items-per-page="15"
			:page.sync="currentPage"
			:server-items-length="totalItems"
			@pagination="paginate"
			:footer-props="footerProps"
		>
			<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
				<slot :name="slot" v-bind="scope" />
			</template>

			<v-alert :value="true" color="error" icon="warning" slot="no-results">
				Your search for "{{ keyword }}" found no
				results.
			</v-alert>
		</v-data-table>
	</v-card>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
	data() {
		return {
			current_page: 1,
			total_items: null,
			action: "",
			keyword: "",
			search_action: false
		};
	},
	props: {
		icon: String,
		tableTitle: String,
		tableHeaders: Array,
		dataUrl: String,
		tableBtnTitle: String,
		tableForm: String,
		tableViewComponent: String,
		tableBtnDisable: Boolean
	},
	mounted() {
		if (this.$props.tableForm === "productForm") {
			this.$root.$on("barcodeScan", sku => {
				this.keyword = sku;
				this.search();
			});
		}
	},
	beforeDestroy() {
		if (this.$props.tableForm === "productForm") {
			this.$root.$off("barcodeScan");
		}
	},
	computed: {
		...mapState("dialog", ["interactive_dialog"]),

		dialog: {
			get() {
				return this.interactive_dialog;
			},
			set(value) {
				this.setDialog(value);
			}
		},
		searchAction: {
			get() {
				return this.search_action;
			},
			set(value) {
				this.search_action = value;
			}
		},
		footerProps() {
			return {
				"disable-pagination": this.loading,
				"disable-items-per-page": true,
				options: { itemsPerPage: 15, page: this.currentPage }
			};
		},
		totalItems: {
			get() {
				return this.total_items;
			},
			set(value) {
				this.total_items = value;
			}
		},
		currentPage: {
			get() {
				return this.current_page;
			},
			set(value) {
				this.current_page = value;
			}
		},
		...mapState("datatable", {
			rows: "rows",
			loading: "loading"
		})
	},
	methods: {
		...mapMutations("dialog", ["setDialog", "resetDialog"]),

		dialogEvent(event) {
			this.resetDialog();
		},

		search(e, page = false) {
			if (this.keyword.length > 2 || this.searchAction) {
				this.setLoading(true);

				if (!page) {
					this.searchAction = this.keyword;
				} else {
					if (!this.keyword) {
						this.keyword = this.searchAction;
					}
				}

				let payload = {
					model: this.dataUrl,
					page: page || 1,
					keyword: this.keyword,
					dataTable: true
				};

				this.searchRequest(payload)
					.then(response => {
						this.setRows(response.data);

						if (response.total !== this.totalItems) {
							this.totalItems = response.total;
						}
					})
					.finally(() => {
						this.setLoading(false);
					});
			}
		},

		paginate(event) {
			if (this.searchAction) {
				this.search(null, event.page);
			} else {
				this.searchAction = false;

				this.setLoading(true);

				this.getAll({
					model: this.dataUrl,
					page: event ? event.page : this.currentPage,
					dataTable: true
				})
					.then(response => {
						this.setRows(response.data);

						if (response.total !== this.totalItems) {
							this.totalItems = response.total;
						}
					})
					.catch(error => {
						// unhandled error
						console.log(error.response);
					})
					.finally(() => {
						this.setLoading(false);
					});
			}
		},

		createItemDialog() {
			this.dialog = {
				show: true,
				width: 600,
				title: `${this.$props.tableBtnTitle}`,
				titleCloseBtn: true,
				component: this.$props.tableForm,
				persistent: true
			};
		},

		...mapMutations("datatable", {
			setRows: "setRows",
			setLoading: "setLoading"
		}),

		...mapActions({
			getAll: "getAll",
			searchRequest: "search"
		})
	}
};
</script>
