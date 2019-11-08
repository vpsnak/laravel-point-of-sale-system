<template>
	<v-container v-if="customerData">
		<v-row>
			<v-col cols="12">
				<v-card>
					<v-card-title>Billing Information</v-card-title>
					<v-card-text>
						<customerForm :model="customerData" @submit="submit"></customerForm>
						<!-- <div class="subtitle-1">First Name: {{customerData.first_name}}</div>
						<div class="subtitle-1">Last Name: {{customerData.last_name}}</div>
						-->

						<!-- <div class="subtitle-1">
							Email:
							<a :href="'mailto:' + customerData.email">{{customerData.email}}</a>
						</div>
						<div class="subtitle-1">Created at: {{customerData.created_at}}</div>
						<div class="subtitle-1">Updated at: {{customerData.updated_at}}</div>-->
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="customerData.addresses.length > 0">
				<!-- <v-card>
				<v-card-title>Addreses</v-card-title>-->
				<!-- <v-card-text>
						<v-simple-table dense>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">First name</th>
										<th class="text-left">Last name</th>
										<th class="text-left">Street</th>
										<th class="text-left">Street 2</th>
										<th class="text-left">City</th>
										<th class="text-left">Country id</th>
										<th class="text-left">Region</th>
										<th class="text-left">Post code</th>
										<th class="text-left">Phone</th>
										<th class="text-left">Company</th>
										<th class="text-left">Vat ID</th>
										<th class="text-left">Delivery date</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="address in customerData.addresses" :key="address.id">
										<td>{{ address.first_name }}</td>
										<td>{{ address.last_name }}</td>
										<td>{{ address.street }}</td>
										<td>{{ address.street2 }}</td>
										<td>{{ address.city }}</td>
										<td>{{ address.country_id }}</td>
										<td>{{ address.region }}</td>
										<td>{{ address.postcode }}</td>
										<td>
											<a :href="'tel:' + address.phone">{{ address.phone }}</a>
										</td>
										<td>{{ address.company }}</td>
										<td>{{ address.vat_id }}</td>
										<td>{{ address.deliverydate }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
				</v-card-text>-->
				<!-- </v-card> -->
				<v-card>
					<v-toolbar flat color="primary" dark>
						<v-toolbar-title>Addresses</v-toolbar-title>
					</v-toolbar>
					<v-tabs vertical>
						<v-tab v-for="index in customerData.addresses.length" :key="index">Address {{index}}</v-tab>
						<v-tab-item v-for="address in customerData.addresses" :key="address.id">
							<v-card flat>
								<v-card-text>
									<addressForm @submit="submit" :model="address"></addressForm>
								</v-card-text>
							</v-card>
						</v-tab-item>
					</v-tabs>
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
			customer: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "customers",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.customer = result;
			});
	},
	computed: {
		customerData() {
			return this.customer;
		}
	},
	methods: {
		submit() {
			this.$emit("submit", {
				getRows: true,
				model: "customers",
				notification: {
					msg: "Customer updated successfully",
					type: "success"
				}
			});
		},
		...mapActions({
			getOne: "getOne"
		})
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
