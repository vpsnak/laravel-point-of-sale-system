<template>
	<div>
		<div class="d-flex justify-center">
			<v-radio-group v-model="shipping.method" @change="selectedShippingMethod" row>
				<v-radio label="Retail" value="retail"></v-radio>
				<v-radio label="In store pickup" value="pickup" :disabled="! customer"></v-radio>
				<v-radio label="Shipping" value="delivery" :disabled="! customer"></v-radio>
			</v-radio-group>
		</div>
		<v-row v-if="shipping.method !== 'retail'">
			<v-col v-if="shipping.method === 'delivery'" cols="12" lg="6" offset-lg="3">
				<v-combobox
					:items="addresses"
					prepend-icon="mdi-map-marker"
					label="Address"
					v-model="shipping.address"
					:item-text="getAddressText"
					return-object
				></v-combobox>
			</v-col>
			<v-col cols="6" lg="3" offset-lg="3">
				<v-menu
					v-model="datePicker"
					:close-on-content-click="false"
					:nudge-right="40"
					transition="scale-transition"
					offset-y
					min-width="290px"
				>
					<template v-slot:activator="{ on }">
						<v-text-field v-model="shipping.date" label="Date" prepend-icon="event" v-on="on" readonly></v-text-field>
					</template>
					<v-date-picker v-model="shipping.date" @input="datePicker = false" :min="new Date().toJSON()"></v-date-picker>
				</v-menu>
			</v-col>
			<v-col cols="6" lg="3">
				<v-menu
					ref="menu"
					v-model="timePicker"
					:close-on-content-click="false"
					:nudge-right="40"
					:return-value.sync="shipping.time"
					transition="scale-transition"
					offset-y
					max-width="290px"
					min-width="290px"
				>
					<template v-slot:activator="{ on }">
						<v-text-field
							v-model="shipping.time"
							label="At"
							prepend-icon="access_time"
							v-on="on"
							readonly
						></v-text-field>
					</template>
					<v-time-picker
						v-if="timePicker"
						v-model="shipping.time"
						full-width
						@click:minute="$refs.menu.save(shipping.time)"
					></v-time-picker>
				</v-menu>
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
export default {
	data() {
		return {
			datePicker: false,
			timePicker: false,
			shipping: {
				address: undefined,
				notes: "",
				method: "retail",
				date: undefined,
				time: undefined,
				cost: 0
			}
		};
	},
	computed: {
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
		selectedShippingMethod() {
			if (this.shipping.method !== "retail") {
				this.shipping.cost = 0;
			}
			this.$emit("shipping", this.shipping);
		},
		clear() {
			this.shipping = {
				address: undefined,
				method: "retail",
				date: undefined,
				time: undefined,
				cost: 0
			};
		}
	},
	beforeDestroy() {
		this.$off("shipping");
	}
};
</script>