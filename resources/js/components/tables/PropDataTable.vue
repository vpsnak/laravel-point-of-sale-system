<template>
	<v-card>
		<v-card-title>
			{{ this.title }}
			<v-spacer></v-spacer>
			<v-text-field append-icon="search" hide-details label="Search" single-line v-model="search"></v-text-field>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-btn
				:disabled="btnDisable"
				color="primary"
				@click.stop="showCreateDialog = true"
			>{{this.btnTitle }}</v-btn>
		</v-card-title>
		<v-data-table :headers="headers" :items="rows" :loading="loading" :search="search">
			<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
				<slot :name="slot" v-bind="scope" />
			</template>
			<template v-slot:item.action="{ item }">
				<v-btn :disabled="btnDisable" @click.stop="editItem(item)" class="mr-2" icon>
					<v-icon small>edit</v-icon>
				</v-btn>
				<v-btn :disabled="btnDisable" @click="deleteConfirmation = true , selectedItem=item" icon>
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
		<!-- view/edit dialog -->
		<interactiveDialog
			v-if="showEditDialog"
			:show="showEditDialog"
			title="Edit item"
			:width="600"
			:component="form"
			:model="defaultObject"
			@action="result"
			persistent
			action="edit"
			titleCloseBtn
		></interactiveDialog>

		<interactiveDialog
			v-if="showCreateDialog"
			:show="showCreateDialog"
			:model="newDefaultObject"
			:component="form"
			:title="btnTitle"
			@action="result"
			cancelBtnTxt="Close"
		></interactiveDialog>

		<interactiveDialog
			v-if="deleteConfirmation"
			:show="deleteConfirmation"
			title="Confirm delete"
			content="Are you sure you want to delete this item?"
			action="confirmation"
			cancelBtnTxt="No"
			confirmationBtnTxt="Yes"
			@action="deleteEvent"
		/>
	</v-card>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
	data() {
		return {
			showCreateDialog: false,
			showEditDialog: false,
			deleteConfirmation: false,
			action: "",
			defaultObject: {},
			newDefaultObject: {},
			search: "",
			selectedItem: {}
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
		})
	},
	methods: {
		editItem(item) {
			this.defaultObject = item;
			this.action = "edit";
			this.showEditDialog = true;
		},

		deleteEvent(event) {
			if (event) {
				this.deleteItem();
			}
			this.deleteConfirmation = false;
		},
		deleteItem() {
			console.log(this.selectedItem.id);
			this.deleteRow({
				url: this.dataUrl + "/" + this.selectedItem.id,
				data: {
					id: this.selectedItem.id
				}
			});
		},
		result(event) {
			console.log(event);
			this.showCreateDialog = false;
			this.showEditDialog = false;
			this.showDeleteDialog = false;
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
