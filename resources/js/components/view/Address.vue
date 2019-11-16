<template>
	<v-container v-if="addressData">
		<v-row>
			<v-col cols="12">
				<v-card>
					<v-card-title>{{addressData.first_name}} {{addressData.last_name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Street: {{addressData.street}}</div>
						<div class="subtitle-1">Street 2: {{addressData.street2}}</div>
						<div class="subtitle-1">City: {{addressData.city}}</div>
						<div class="subtitle-1">Country id: {{addressData.country_id}}</div>
						<div class="subtitle-1">Region: {{addressData.region_id.default_name}}</div>
						<div class="subtitle-1">Post code: {{addressData.postcode}}</div>
						<div class="subtitle-1">Phone: {{addressData.phone}}</div>
						<div class="subtitle-1">Company: {{addressData.company}}</div>
						<div class="subtitle-1">Vat ID: {{addressData.vat_id}}</div>
						<div class="subtitle-1">Delivery date: {{addressData.delivery_date}}</div>
						<div class="subtitle-1">Default Billing: {{addressData.billing ? 'Yes' : 'No'}}</div>
						<div class="subtitle-1">Default Shipping: {{addressData.shipping ? 'Yes' : 'No'}}</div>
						<div class="subtitle-1">Created at: {{addressData.created_at}}</div>
						<div class="subtitle-1">Updated at: {{addressData.updated_at}}</div>
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
			address: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "addresses",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.address = result;
			});
	},
	computed: {
		addressData() {
			return this.address;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
