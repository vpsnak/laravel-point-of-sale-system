<template>
	<div>
		<div class="d-flex justify-center">
			<v-radio-group v-model="shipping.method" row>
				<v-radio label="Retail" value="retail"></v-radio>
				<v-radio label="In store pickup" value="pickup" :disabled="! customer"></v-radio>
				<v-radio label="Delivery" value="delivery" :disabled="! customer"></v-radio>
			</v-radio-group>
		</div>
		<v-row v-if="shipping.method !== 'retail'">
			<v-col v-if="shipping.method === 'delivery'" cols="12" lg="6" offset-lg="3">
				<v-combobox
					@input="getTimeSlots"
					:items="addresses"
					prepend-icon="mdi-map-marker"
					label="Address"
					v-model="shipping.address"
					:item-text="getAddressText"
					return-object
				></v-combobox>
			</v-col>
			<v-col cols="4" lg="2" offset-lg="3">
				<v-menu v-model="datePicker" transition="scale-transition" offset-y min-width="290px">
					<template v-slot:activator="{ on }">
						<v-text-field
							v-model="shipping.date"
							label="Date"
							prepend-icon="event"
							v-on="on"
							readonly
							@input="getTimeSlots"
						></v-text-field>
					</template>
					<v-date-picker v-model="shipping.date" @input="getTimeSlots" :min="new Date().toJSON()"></v-date-picker>
				</v-menu>
			</v-col>
			<v-col cols="4" lg="2" v-if="shipping.method === 'delivery'">
				<v-combobox
					:loading="loading"
					label="At"
					prepend-icon="mdi-clock"
					:items="timeSlots"
					item-text="label"
					v-model="shipping.timeSlotLabel"
					@input="setCost"
				></v-combobox>
			</v-col>
			<v-col cols="4" lg="2" v-if="shipping.method === 'delivery'">
				<v-text-field
					type="number"
					label="Cost"
					:min="0"
					prepend-icon="mdi-currency-usd"
					v-model="shipping.timeSlotCost"
				></v-text-field>
			</v-col>
		</v-row>
		<v-row>
			<v-col cols="12" lg="6" offset-lg="3">
				<v-textarea
					label="Notes"
					prepend-icon="mdi-note-text"
					v-model="shipping.notes"
					counter
					clearable
					no-resize
					outlined
				></v-textarea>
			</v-col>
		</v-row>
	</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			loading: false,
			time_slots: [],
			datePicker: false
		};
	},

	computed: {
		timeSlots: {
			get() {
				return this.time_slots;
			},
			set(value) {
				this.time_slots = value;
			}
		},
		shipping: {
			get() {
				return this.$store.state.cart.shipping;
			},
			set(value) {
				this.$store.state.cart.shipping = value;
			}
		},
		addresses() {
			if (this.customer) {
				return this.customer.addresses;
			}
		},
		customer() {
			if (this.$store.state.cart.order) {
				return this.$store.state.cart.order.customer;
			} else if (this.$store.state.cart.customer) {
				return this.$store.state.cart.customer;
			} else {
				return undefined;
			}
		}
	},

	methods: {
		setCost(item) {
			if (_.has(item, "cost")) {
				this.shipping.timeSlotCost = item.cost;
			} else {
				this.shipping.timeSlotCost = null;
			}
		},
		getTimeSlots() {
			if (
				this.shipping.address &&
				this.shipping.date &&
				this.shipping.method === "delivery"
			) {
				this.loading = true;
				let payload = {
					url: "shipping/timeslot",
					data: {
						postcode: this.shipping.address.postcode,
						date: this.shipping.date
					}
				};
				this.postRequest(payload)
					.then(response => {
						this.timeSlots = response;
					})
					.finally(() => {
						this.loading = false;
					});
			}
		},
		getAddressText(item) {
			return (
				item.street +
				" " +
				item.city +
				" " +
				item.area_code_id +
				" " +
				item.region +
				" " +
				item.country_id
			);
		},
		...mapActions(["postRequest"])
	},
	beforeDestroy() {
		this.$off("shipping");
	}
};
</script>