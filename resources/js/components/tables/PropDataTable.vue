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
				<v-icon :disabled="btnDisable" @click="editItem(item)" class="mr-2" small>edit</v-icon>
				<v-icon :disabled="btnDisable" @click="deleteItem(item)" small>delete</v-icon>
			</template>
			<v-alert
				:value="true"
				color="error"
				icon="warning"
				slot="no-results"
			>Your search for "{{ search }}" found no results.</v-alert>
		</v-data-table>
		<v-dialog max-width="600px" transition="dialog-bottom-transition" v-model="showDialog">
			<v-card>
				<v-card-title>
					<span class="headline">Form</span>
				</v-card-title>
				<v-card-text>
					<v-container>
						<v-row>
							<v-col>
								<component :is="form" />
							</v-col>
						</v-row>
					</v-container>
				</v-card-text>
			</v-card>
		</v-dialog>
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
				this.showDialog = true;
			},

			deleteItem(item) {
				confirm("Are you sure you want to delete this item?") &&
					this.deleteRow({
						url: "customers/" + item.id,
						data: {
							id: item.id
						}
					});
			},

			showFormDialog() {
				this.showDialog = true;
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
				setShowDialog: "setShowDialog",
				setBtnDisable: "setBtnDisable"
			})
		}
	};
</script>
