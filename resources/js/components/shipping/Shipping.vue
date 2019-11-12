<template>
	<div>
		<interactiveDialog
			v-if="addTimeSlotDialog"
			action="update"
			:show="addTimeSlotDialog"
			title="Add time slot"
			icon="mdi-clock"
			component="timeSlotForm"
			@action="closedDialog"
		/>

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
			<v-col v-if="shipping.method === 'delivery'" cols="12" lg="6" offset-lg="3">
				<addressDeliveryForm :model="shipping.address"></addressDeliveryForm>
			</v-col>
		</v-row>
		<v-row v-if="shipping.method !== 'retail'">
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
			<v-col cols="4" lg="3" v-if="shipping.method !== 'retail'">
				<v-select
					:loading="loading"
					label="At"
					prepend-icon="mdi-clock"
					append-outer-icon="mdi-plus"
					:items="timeSlots"
					item-text="label"
					v-model="shipping.timeSlotLabel"
					@input="setCost"
					@click:append-outer="addTimeSlotDialog = true"
				></v-select>
			</v-col>
			<v-col cols="4" lg="1" v-if="shipping.method !== 'retail'">
				<v-text-field
					type="number"
					label="Cost"
					:min="0"
					prepend-icon="mdi-currency-usd"
					v-model="shipping.timeSlotCost"
				></v-text-field>
			</v-col>
		</v-row>
		<v-row v-if="shipping.method !== 'retail'">
			<v-col offset-lg="3" cols="6" lg="2">
				<v-select
					:loading="loading"
					label="Occasion"
					:items="occasions"
					item-text="label"
					v-model="shipping.occasion"
					prepend-icon="mdi-star-face"
				></v-select>
			</v-col>
			<v-col offset-lg="2" cols="6" lg="2" v-if="shipping.method === 'delivery'">
				<v-select
					:loading="loading"
					label="Location"
					:items="locations"
					item-text="label"
					v-model="shipping.location"
					prepend-icon="mdi-city"
				></v-select>
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
			addTimeSlotDialog: false,
			loading: false,
			time_slots: [],
			datePicker: false
		};
	},

	computed: {
		locations() {
			return this.$store.state.cart.locations;
		},
		occasions() {
			return this.$store.state.cart.occasions;
		},
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
				this.$store.state.cart.timeSlotCost = value;
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
		closedDialog(event) {
			if (event && event !== true) {
				this.timeSlots.push(event);
				this.shipping.timeSlotLabel = event.label;
				this.shipping.cost = event.cost;
			}

			this.addTimeSlotDialog = false;
		},
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
				item.first_name +
				" " +
				item.last_name +
				" " +
				item.street +
				" " +
				item.city +
				", " +
				item.address_region +
				", " +
				item.postcode +
				", " +
				item.address_country
			);
		},
		...mapActions(["postRequest"])
	},
	beforeDestroy() {
		this.$off("shipping");
	}
};
</script>