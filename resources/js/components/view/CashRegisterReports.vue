<template>
	<v-container v-if="cashRegisterReportsReportsData">
		<v-row>
			<v-col cols="12">
				<v-card>
					<v-card-title>{{cashRegisterReportsReportsData.report_name}} REPORTS</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Report type: {{ cashRegisterReportsReportsData.report_type }}</div>
						<div class="subtitle-1">Created by: {{ cashRegisterReportsReportsData.created_by }}</div>
						<div
							class="subtitle-1"
						>Cash register ID: {{ cashRegisterReportsReportsData.cash_register_id }}</div>
						<div class="subtitle-1">opening_amount: {{ cashRegisterReportsReportsData.opening_amount }}</div>
						<div class="subtitle-1">closing_amount: {{ cashRegisterReportsReportsData.closing_amount }}</div>
						<div class="subtitle-1">Subtotal: {{ cashRegisterReportsReportsData.subtotal }} $</div>
						<div class="subtitle-1">Tax: {{ cashRegisterReportsReportsData.tax }} $</div>
						<div class="subtitle-1">Total: {{ cashRegisterReportsReportsData.total }} $</div>
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
			cashRegisterReportsReports: null
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
		cashRegisterReportsReportsData() {
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
