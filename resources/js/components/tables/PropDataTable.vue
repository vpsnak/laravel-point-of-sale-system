<template>
	<v-card>
		<v-card-title>
			{{ this.title }}
			<v-spacer></v-spacer>
			<v-text-field append-icon="search" hide-details label="Search" single-line v-model="search"></v-text-field>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-btn :disabled="btnDisable" color="primary" @click="showFormDialog">{{this.btnTitle }}</v-btn>
		</v-card-title>
		<v-data-table :headers="headers" :items="rows" :loading="loading" :search="search">
			<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
				<slot :name="slot" v-bind="scope" />
			</template>
			<template v-slot:item.action="{ item }">
				<v-btn :disabled="btnDisable" @click="editItem(item)" class="mr-2" icon>
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
		<interactiveDialog :title="this.btnTitle" :width="600" :component="form"></interactiveDialog>
	</v-card>
</template>

<script>
	import { mapActions, mapMutations, mapState } from "vuex";

	export default {
		data() {
			return {
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
			showDialog: {
				get() {
					return this.$store.state.datatable.showDialog;
				},
				set(value) {
					this.setShowDialog(value);
				}
			}
		},
		methods: {
			editItem(item) {
				this.editedIndex = this.rows.indexOf(item);
				this.editedItem = Object.assign({}, item);
				this.showFormDialog();
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

			showFormDialog() {
				this.$store.state.interactiveDialog = true;
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
			})
		}
	};
</script>
