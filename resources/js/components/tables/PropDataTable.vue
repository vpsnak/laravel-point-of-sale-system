<template>
	<v-card>
		<v-card-title>
			{{ this.title }}
			<v-spacer></v-spacer>
			<v-text-field append-icon="search" hide-details label="Search" single-line v-model="search"></v-text-field>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-btn :disabled="btnDisable" color="primary" @click="showInteractiveDialog">{{this.btnTitle }}</v-btn>
		</v-card-title>
		<v-data-table :headers="headers" :items="rows" :loading="loading" :search="search">
			<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
				<slot :name="slot" v-bind="scope" />
			</template>
			<template v-slot:item.action="{ item }">
				<v-btn :disabled="btnDisable" @click.stop="editItem(item)" class="mr-2" icon>
					<v-icon small>edit</v-icon>
				</v-btn>
				<v-btn :disabled="btnDisable" @click="deleteItem(item)" icon>
					<v-icon small>delete</v-icon>
				</v-btn>
			</template>
			<v-alert
				:value="true"
				color="error"
				icon="warning"
				slot="no-results"
			>Your search for "{{ search }}" found no results.</v-alert>
		</v-data-table>
		<interactiveDialog
			v-if="visibility"
			:show="visibility"
			:title="this.btnTitle"
			:width="600"
			:component="form"
			:model="testObject2"
			@action="result"
			persistent
			action="confirmation"
		></interactiveDialog>
	</v-card>
</template>

<script>
	import { mapActions, mapMutations, mapState } from "vuex";

	export default {
		data() {
			return {
				showInteractiveDialog: false,
				testObject2: {},
				search: ""
			};
		},
		props: [
			"tableTitle",
			"tableHeaders",
			"dataUrl",
			"tableBtnTitle",
			"tableForm",
			"tableBtnDisable"
		],
		mounted() {
			this.setHeaders(this.tableHeaders);
			this.getRows({
				url: this.dataUrl
			});
			this.setTitle(this.tableTitle);
			this.setBtnTitle(this.tableBtnTitle);
			this.setBtnDisable(this.tableBtnDisable);
			this.setForm(this.tableForm);
		},
		computed: {
			...mapState("datatable", {
				title: "title",
				headers: "headers",
				rows: "rows",
				loading: "loading",
				btnTitle: "btnTitle",
				form: "form",
				btnDisable: "btnDisable"
			}),
			visibility: {
				get() {
					return this.showInteractiveDialog;
				},
				set(value) {
					this.showInteractiveDialog = value;
				}
			}
		},
		methods: {
			editItem(item) {
				this.getOne({
					model: this.dataUrl,
					data: {
						id: item.id
					}
				}).then(object => {
					this.testObject2 = { ...object };
					this.visibility = true;
				});
				this.editedIndex = this.rows.indexOf(item);
				this.editedItem = Object.assign({}, item);
			},

			deleteItem(item) {
				confirm("Are you sure you want to delete this item?") &&
					this.deleteRow({
						url: this.dataUrl + "/" + item.id,
						data: {
							id: item.id
						}
					});
			},
			result(event) {
				console.log(event);
				this.visibility = false;
			},

			...mapActions("datatable", {
				getRows: "getRows",
				deleteRow: "deleteRow"
			}),
			...mapMutations("datatable", {
				setHeaders: "setHeaders",
				setTitle: "setTitle",
				setBtnTitle: "setBtnTitle",
				setForm: "setForm",
				setBtnDisable: "setBtnDisable"
			}),
			...mapActions({
				getAll: "getAll",
				getOne: "getOne",
				create: "create",
				delete: "delete"
			})
		}
	};
</script>
