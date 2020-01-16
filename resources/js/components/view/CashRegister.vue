<template>
	<v-container>
		<v-row v-if="cashRegisterData">
			<v-col cols="12" md="5">
				<v-card>
					<v-card-title>{{cashRegisterData.name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Created at: {{ cashRegisterData.created_at }}</div>
						<div class="subtitle-1">Updated at: {{ cashRegisterData.updated_at }}</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="7" v-if="cashRegisterData.store">
				<v-card>
					<v-card-title>Store</v-card-title>
					<v-card-text>
						<v-simple-table dense>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">ID</th>
										<th class="text-left">Name</th>
										<th class="text-left">Tax</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{ cashRegisterData.store.id }}</td>
										<td>{{ cashRegisterData.store.name }}</td>
										<td>{{ cashRegisterData.store.tax.name }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="cashRegisterData.payments.length > 0">
				<v-card>
					<v-card-title>Payments</v-card-title>
					<v-card-text>
						<v-simple-table dense>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">Name</th>
										<th class="text-left">Amount</th>
										<th class="text-left">Status</th>
										<th class="text-left">Refunded</th>
										<th class="text-left">Created by</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="payment in cashRegisterData.payments" :key="payment.id">
										<td>{{ payment.payment_type.name }}</td>
										<td>{{ payment.amount }}</td>
										<td>{{ payment.status }}</td>
										<td>{{ payment.refunded }}</td>
										<td>{{ payment.created_by }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-else>
				<v-card-title>Payments</v-card-title>
				<v-card-text>
					There are no paymens
					that have been made by this cash register
				</v-card-text>
			</v-col>
		</v-row>
		<v-row v-else>
			<v-col cols="12" align="center" justify="center">
				<v-progress-circular indeterminate color="secondary"></v-progress-circular>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Int32Array | null
	},
	data() {
		return {
			cashRegister: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "cash-registers",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.cashRegister = result;
			});
	},
	computed: {
		cashRegisterData() {
			return this.cashRegister;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
