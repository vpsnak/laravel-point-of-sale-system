<template>
	<v-container v-if="cashRegisterReportsData">
		<v-row>
			<v-col cols="12">
				<v-card>
					<v-card-title>{{cashRegisterReportsData.report_name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Report type: {{ cashRegisterReportsData.report_type }}</div>
						<div class="subtitle-1">Created by: {{ cashRegisterReportsData.created_by }}</div>
						<div class="subtitle-1">Cash register ID: {{ cashRegisterReportsData.cash_register_id }}</div>
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
			cashRegisterReports: null
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
