<template>
	<v-container v-if="orderData">
		<v-row>
			<v-col cols="12" md="5">
				<v-card>
					<v-card-title>Order #{{orderData.id}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Status: {{orderData.status}}</div>
						<div class="subtitle-1">Store: {{orderData.store_id.name}}</div>
						<div class="subtitle-1">Created: {{orderData.created_at}}</div>
						<div class="subtitle-1">Created by: {{orderData.created_by.email}}</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="7">
				<v-simple-table>
					<template v-slot:default>
						<thead>
							<tr>
								<th class="text-left">Name</th>
								<th class="text-left">Sku</th>
								<th class="text-left">Price</th>
								<th class="text-left">Qty</th>
								<th class="text-left">Notes</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="item in orderData.items" :key="item.id">
								<td>{{ item.name }}</td>
								<td>{{ item.sku }}</td>
								<td>{{ item.price }}</td>
								<td>{{ item.qty }}</td>
								<td>{{ item.notes }}</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
			</v-col>
			<v-col cols="12" md="5" v-if="orderData.shipping_type">
				<v-card>
					<v-card-title>Delivery</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Delivery Type: {{orderData.shipping_type}}</div>
						<div
							class="subtitle-1"
							v-if="orderData.delivery_date"
						>Delivery Date: {{orderData.delivery_date}}</div>
						<div class="subtitle-1">Delivery Cost: {{orderData.shipping_cost}} $</div>
						<div class="subtitle-1" v-if="orderData.location">Location: {{orderData.location}}</div>
						<div class="subtitle-1" v-if="orderData.occasion">Occasion: {{orderData.occasion}}</div>
						<div
							class="subtitle-1"
							v-if="orderData.shipping_address"
						>Delivery Address: {{orderData.shipping_address ? 'Yes' : 'No'}}</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="7" v-if="orderData.notes">
				<v-card>
					<v-card-title>Notes</v-card-title>
					<v-card-text>
						<p class="body-1">{{orderData.notes}}</p>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="7" v-if="orderData.shipping_address">
				<v-card>
					<v-card-title>Shipping Address</v-card-title>
					<v-card-text>
						<div class="subtitle-1">First name: {{orderData.shipping_address.first_name}}</div>
						<div class="subtitle-1">Last name: {{orderData.shipping_address.last_name}}</div>
						<div class="subtitle-1">Street: {{orderData.shipping_address.street}}</div>
						<div class="subtitle-1">Street 2: {{orderData.shipping_address.street2}}</div>
						<div class="subtitle-1">City: {{orderData.shipping_address.city}}</div>
						<div class="subtitle-1">Country id: {{orderData.shipping_address.country_id}}</div>
						<div class="subtitle-1">Region: {{orderData.shipping_address.region_id.default_name}}</div>
						<div class="subtitle-1">Post code: {{orderData.shipping_address.postcode}}</div>
						<div class="subtitle-1">Phone: {{orderData.shipping_address.phone}}</div>
						<div class="subtitle-1">Company: {{orderData.shipping_address.company}}</div>
						<div class="subtitle-1">Vat ID: {{orderData.shipping_address.vat_id}}</div>
						<div
							class="subtitle-1"
						>Default Shipping: {{orderData.shipping_address.shipping ? 'Yes' : 'No'}}</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="5">
				<v-card>
					<v-card-title>Totals</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Subtotal: {{orderData.subtotal}} $</div>
						<div class="subtitle-1">Delivery Fees: {{orderData.shipping_cost}} $</div>
						<div
							class="subtitle-1"
						>Tax ({{orderData.tax}}%) : {{(orderData.total - orderData.subtotal - orderData.shipping_cost)}} $</div>
						<div class="headline">Total: {{orderData.total}} $</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="orderData.payments.length">
				<v-card>
					<v-card-title>Payment History</v-card-title>
					<v-card-text>
						<v-simple-table>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">Operator</th>
										<th class="text-left">Type</th>
										<th class="text-left">Status</th>
										<th class="text-left">Amount</th>
									</tr>
								</thead>
								<tbody>
									<tr :key="item.id" v-for="item in orderData.payments">
										<td>{{ item.created_by.name }}</td>
										<td>{{ item.payment_type.name }}</td>
										<td>{{ item.status }}</td>
										<td>{{ item.amount }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="orderData.customer">
				<v-card>
					<v-card-title>Customer</v-card-title>
					<v-card-text>
						<v-simple-table>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">First name</th>
										<th class="text-left">Last name</th>
										<th class="text-left">Email</th>
										<th class="text-left">House account</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{ orderData.customer.first_name }}</td>
										<td>{{ orderData.customer.last_name }}</td>
										<td>{{ orderData.customer.email }}</td>
										<td>{{ orderData.customer.house_account_status ? 'Yes' : 'No' }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="orderData.customer">
				<v-card>
					<v-card-title>Customer Addresses</v-card-title>
					<v-card-text>
						<v-simple-table>
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
										<th class="text-left">Default Billing</th>
										<th class="text-left">Default Shipping</th>
									</tr>
								</thead>
								<tbody>
									<tr :key="item.id" v-for="item in orderData.customer.addresses">
										<td>{{ item.first_name }}</td>
										<td>{{ item.last_name }}</td>
										<td>{{ item.street }}</td>
										<td>{{ item.street2 }}</td>
										<td>{{ item.city }}</td>
										<td>{{ item.country_id }}</td>
										<td>{{ item.region_id.default_name }}</td>
										<td>{{ item.postcode }}</td>
										<td>{{ item.phone }}</td>
										<td>{{ item.billing ? 'Yes' : 'No'}}</td>
										<td>{{ item.shipping ? 'Yes' : 'No'}}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
	<v-container v-else>
		<v-row>
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
			order: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "orders",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.order = result;
			});
	},
	computed: {
		orderData() {
			return this.order;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
