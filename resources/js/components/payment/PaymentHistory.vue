<template>
	<v-row>
		<interactiveDialog
			v-if="dialog.show"
			:show="dialog.show"
			:title="dialog.title"
			:titleCloseBtn="dialog.titleCloseBtn"
			:icon="dialog.icon"
			:width="dialog.width"
			:component="dialog.component"
			:content="dialog.content"
			:model="dialog.model"
			@action="dialogEvent"
			:persistent="dialog.persistent"
		></interactiveDialog>

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
								@click="refundDialog(item)"
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
			dialog: {
				show: false,
				width: 600,
				icon: "",
				title: "",
				titleCloseBtn: false,
				component: "",
				content: "",
				model: "",
				persistent: false
			},
			paymentID: null,
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
		dialogEvent(event) {
			if (event) {
				this.refund();
			}

			this.resetDialog();
		},
		refund() {
			let payload = {
				model: "payments",
				id: this.paymentID
			};

			this.delete(payload).then(response => {
				this.$emit("refund", response);
			});
		},

		refundDialog(item) {
			this.dialog = {
				show: true,
				width: 600,
				title: `Verify your password to refund payment #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-lock-alert",
				component: "passwordForm",
				model: { action: "verify" },
				persistent: true
			};

			this.paymentID = item.id;

			this.action = "cancelOrder";
		},

		resetDialog() {
			this.dialog = {
				show: false,
				width: 600,
				title: "",
				titleCloseBtn: false,
				icon: "",
				component: "",
				content: "",
				model: "",
				persistent: false
			};

			this.action = "";
		},

		...mapActions(["delete"]),

		beforeDestroy() {
			this.$off("refund");
		}
	}
};
</script>
