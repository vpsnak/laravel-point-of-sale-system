<template>
	<v-row>
		<interactiveDialog
			v-if="refundConfirmation"
			:show="refundConfirmation"
			title="Confirm refund"
			content="Are you sure you want to make a refund?"
			action="confirmation"
			cancelBtnTxt="No"
			confirmationBtnTxt="Yes"
			@action="refundEvent"
		/>
		<v-col cols="12">
			<h3 class="mb-3">Payment history</h3>
			<v-data-table
				:headers="headers"
				:items="paymentHistory"
				class="elevation-1"
				disable-pagination
				disable-filtering
				hide-default-footer
				:loading="loading"
			>
				<template v-slot:item.actions="{ item }">
					<v-tooltip bottom>
						<template v-slot:activator="{ on }">
							<v-btn
								@click="refundConfirmation = true, refundItem = item"
								icon
								v-on="on"
								v-if="item.status === 'approved' && item.refunded !== 1"
							>
								<v-icon v-if="item.payment_type.type === 'cash'">mdi-cash-refund</v-icon>
								<v-icon v-else>mdi-credit-card-refund</v-icon>
							</v-btn>
						</template>
						<span>Refund</span>
					</v-tooltip>
				</template>
			</v-data-table>
		</v-col>
	</v-row>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		paymentHistory: Array,
		loading: Boolean
	},
	data() {
		return {
			refundConfirmation: null,
			refundItem: null,
			headers: [
				{
					text: "Operator",
					value: "created_by.name"
				},
				{
					text: "Date",
					value: "created_at"
				},
				{
					text: "Type",
					value: "payment_type.name"
				},
				{
					text: "Status",
					value: "status"
				},
				{
					text: "Amount (USD)",
					value: "amount"
				},
				{
					text: "Actions",
					value: "actions"
				}
			]
		};
	},
	methods: {
		refundEvent(event) {
			if (event) {
				this.refund();
			}
			this.refundConfirmation = false;
		},
		refund() {
			let payload = {
				model: "payments",
				id: this.refundItem.id
			};

			this.delete(payload).then(response => {
				this.$emit("refund", response);
			});
		},
		...mapActions(["delete"]),

		beforeDestroy() {
			this.$off("refund");
		}
	}
};
</script>
