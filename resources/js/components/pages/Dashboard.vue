<template>
	<div>
		<v-btn @click.stop="showConfirmationDialog = true">Confirmation dialog</v-btn>

		<interactiveDialog
			v-if="showConfirmationDialog"
			action="confirmation"
			:show="showConfirmationDialog"
			title="Confirmation dialog"
			content="Confirmation dialog <h2><s>html</s></h2> content"
			@action="confirmationResult"
			persistent
			cancelBtnTxt="No"
			confirmationBtnTxt="Yes"
		/>

		<v-btn @click.stop="showInfoDialog = true">Update dialog</v-btn>

		<interactiveDialog
			v-if="showInfoDialog"
			action="update"
			:show="showInfoDialog"
			title="Update dialog"
			component="paymentActions"
			@action="editDialog"
		/>

		<v-btn @click.stop="showReadDialog = true">Read dialog</v-btn>

		<interactiveDialog
			v-if="showReadDialog"
			action="read"
			:show="showReadDialog"
			title="Read dialog"
			component="categoryForm"
			:model="category"
			@action="readResult"
			cancelBtnTxt="Close"
		/>

		<v-btn @click.stop="showEditDialog = true">Update dialog</v-btn>

		<interactiveDialog
			v-if="showEditDialog"
			action="update"
			:show="showEditDialog"
			title="Update dialog"
			component="paymentActions"
			@action="editDialog"
		/>
	</div>
</template>

<script>
export default {
	data() {
		return {
			showConfirmationDialog: false,
			showReadDialog: false,
			showEditDialog: false,
			showInfoDialog: false,

			category: {
				name: "Category 1",
				in_product_listing: true
			}
		};
	},

	methods: {
		confirmationResult(event) {
			console.log("confirmation dialog result: " + event);
			this.showConfirmationDialog = false;
		},
		readResult(event) {
			console.log("read dialog result: " + event);
			this.showReadDialog = false;
		},
		editDialog(event) {
			console.log("update dialog result: " + event);
			this.showEditDialog = false;
		}
	}
};
</script>