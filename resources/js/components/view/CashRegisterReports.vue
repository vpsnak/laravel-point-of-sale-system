<template>
	<v-container v-if="cashRegisterReportsData">
		<v-row>
			<v-col cols="12" md="5">
				<v-card>
					<v-card-title>{{cashRegisterReportsData.report_name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Report type: {{ cashRegisterReportsData.report_type }}</div>
						<div class="subtitle-1">Created by: {{ user.name }}</div>
						<div class="subtitle-1">Cash register: {{ cash_register.name }}</div>
						<div
							class="subtitle-1"
						>Opening amount: {{parseFloat(cashRegisterReportsData.opening_amount).toFixed(2)}} $</div>
						<div
							class="subtitle-1"
						>Closing_amount: {{parseFloat(cashRegisterReportsData.closing_amount).toFixed(2)}} $</div>
						<div
							class="subtitle-1"
						>Subtotal: {{ parseFloat(cashRegisterReportsData.subtotal).toFixed(2) }} $</div>
						<div class="subtitle-1">Tax: {{ parseFloat(cashRegisterReportsData.tax).toFixed(2) }} $</div>
						<div class="subtitle-1">Total: {{ parseFloat(cashRegisterReportsData.total).toFixed(2) }} $</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="7">
				<v-card>
					<v-card-text>
						<div
							class="subtitle-1"
						>Cash total: {{ parseFloat(cashRegisterReportsData.cash_total).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Gift card total: {{ parseFloat(cashRegisterReportsData.gift_card_total).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Credit card total: {{ parseFloat(cashRegisterReportsData.credit_card_total).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Pos Terminal Total: {{ parseFloat(cashRegisterReportsData.pos_terminal_total).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Change Total: {{ parseFloat(cashRegisterReportsData.change_total).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Cash tax: {{ parseFloat(cashRegisterReportsData.cash_tax).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Gift Card tax: {{ parseFloat(cashRegisterReportsData.gift_card_tax).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Credit Card tax: {{ parseFloat(cashRegisterReportsData.credit_card_tax).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Pos Terminal tax: {{ parseFloat(cashRegisterReportsData.credit_card_tax).toFixed(2) }} $</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="5">
				<v-card>
					<v-card-title>Order</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Order count: {{ cashRegisterReportsData.order_count }}</div>
						<div class="subtitle-1">Order product count: {{ cashRegisterReportsData.order_product_count}}</div>
						<div class="subtitle-1">Order refund count: {{ cashRegisterReportsData.order_refund_count }}</div>
						<div
							class="subtitle-1"
						>Order refund total: {{ parseFloat(cashRegisterReportsData.order_refund_total).toFixed(2) }} $</div>
						<div
							class="subtitle-1"
						>Order complete count: {{cashRegisterReportsData.order_complete_count }}</div>
						<div
							class="subtitle-1"
						>Order complete total: {{ parseFloat(cashRegisterReportsData.order_complete_total).toFixed(2) }} $</div>
						<div class="subtitle-1">Order retail count: {{ cashRegisterReportsData.order_retail_count }}</div>
						<div
							class="subtitle-1"
						>Order in store count: {{ cashRegisterReportsData.order_in_store_count}}</div>
						<div
							class="subtitle-1"
						>Order delivery count: {{ cashRegisterReportsData.order_delivery_count}}</div>
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
	<div v-else>Loading...</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Int32Array | null
	},
	data() {
		return {
			cashRegisterReports: null,
			cash_register: null,
			user: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "cash-register-reports",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.cashRegisterReports = result;
				this.getOne({
					model: "users",
					data: {
						id: this.cashRegisterReportsData.created_by
					}
				}).then(response => {
					this.user = response;
				});
				this.getOne({
					model: "cash-registers",
					data: {
						id: this.cashRegisterReportsData.cash_register_id
					}
				}).then(response => {
					this.cash_register = response;
				});
			});
	},
	computed: {
		cashRegisterReportsData() {
			return this.cashRegisterReports;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
