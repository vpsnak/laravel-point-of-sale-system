<template>
	<div>
		<interactiveDialog
			v-if="addTimeSlotDialog"
			:show="addTimeSlotDialog"
			title="Add time slot"
			:titleCloseBtn="true"
			icon="mdi-clock"
			component="timeSlotForm"
			@action="closedDialog"
			persistent
		/>

		<interactiveDialog
			v-if="dialog.show"
			:show="dialog.show"
			:title="dialog.title"
			:titleCloseBtn="dialog.titleCloseBtn"
			:icon="dialog.icon"
			:fullscreen="dialog.fullscreen"
			:width="dialog.width"
			:component="dialog.component"
			:content="dialog.content"
			:model="dialog.model"
			@action="dialogEvent"
			:persistent="dialog.persistent"
		></interactiveDialog>
		<ValidationObserver ref="shippingValidation">
			<v-form>
				<v-row v-if="shipping.method !== 'retail'" align="center">
					<v-col v-if="shipping.method === 'delivery'" :cols="10" :lg="6" :offset-lg="3" class="pa-2">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Billing address">
							<v-combobox
								@change="validate"
								:items="addresses"
								prepend-icon="mdi-receipt"
								label="Billing address"
								v-model="billingAddress"
								:item-text="getAddressText"
								:error-messages="errors"
								:success="valid"
								return-object
							></v-combobox>
						</ValidationProvider>
					</v-col>

					<div v-if="shipping.method === 'delivery'">
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn
									:disabled="!billingAddress"
									icon
									v-on="on"
									@click="addressDialog('billing', billingAddress)"
								>
									<v-icon>mdi-pencil</v-icon>
								</v-btn>
							</template>
							<span>Edit selected address</span>
						</v-tooltip>
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn icon v-on="on" @click="addressDialog('billing', null)">
									<v-icon>mdi-plus</v-icon>
								</v-btn>
							</template>
							<span>New billing address</span>
						</v-tooltip>
					</div>

					<v-col v-if="shipping.method === 'delivery'" :cols="10" :lg="6" :offset-lg="3" class="pa-2">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Delivery address">
							<v-combobox
								@input="getTimeSlots"
								@change="validate"
								:items="addresses"
								prepend-icon="mdi-map-marker"
								label="Delivery address"
								v-model="shipping.address"
								:item-text="getAddressText"
								return-object
								:error-messages="errors"
								:success="valid"
							></v-combobox>
						</ValidationProvider>
					</v-col>

					<div v-if="shipping.method === 'delivery'">
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn
									:disabled="!shipping.address"
									icon
									v-on="on"
									@click="addressDialog('delivery', shipping.address)"
								>
									<v-icon>mdi-pencil</v-icon>
								</v-btn>
							</template>
							<span>Edit selected address</span>
						</v-tooltip>
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn icon v-on="on" @click="addressDialog('delivery', null)">
									<v-icon>mdi-plus</v-icon>
								</v-btn>
							</template>
							<span>New delivery address</span>
						</v-tooltip>
					</div>

					<v-col v-if="shipping.method === 'delivery'" cols="12" lg="6" offset-lg="3">
						<addressDeliveryForm v-if="shipping.address" :model="shipping.address" readonly></addressDeliveryForm>
					</v-col>
				</v-row>

				<v-row v-if="shipping.method !== 'retail'">
					<v-col cols="4" lg="2" offset-lg="3" class="pa-1">
						<v-menu v-model="datePicker" transition="scale-transition" offset-y min-width="290px">
							<template v-slot:activator="{ on }">
								<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Date">
									<v-text-field
										v-model="dateFormatted"
										label="Date"
										prepend-icon="event"
										:error-messages="errors"
										:success="valid"
										v-on="on"
										@input="getTimeSlots"
										@blur="parseDate, validate"
										readonly
									></v-text-field>
								</ValidationProvider>
							</template>
							<v-date-picker v-model="shipping.date" @input="getTimeSlots" :min="new Date().toJSON()"></v-date-picker>
						</v-menu>
					</v-col>
					<v-col cols="4" lg="3" v-if="shipping.method !== 'retail'" class="pa-1">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="At">
							<v-select
								:loading="loading"
								label="At"
								prepend-icon="mdi-clock"
								append-outer-icon="mdi-plus"
								:items="timeSlots"
								:error-messages="errors"
								:success="valid"
								item-text="label"
								v-model="shipping.timeSlotLabel"
								@input="setCost"
								@click:append-outer="addTimeSlotDialog = true"
								@change="validate"
							></v-select>
						</ValidationProvider>
					</v-col>
					<v-col cols="4" lg="1" v-if="shipping.method !== 'retail'" class="pa-1">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Cost">
							<v-text-field
								type="number"
								label="Cost"
								:error-messages="errors"
								:success="valid"
								:min="0"
								prepend-icon="mdi-currency-usd"
								v-model="shippingCost"
								@input="validate"
							></v-text-field>
						</ValidationProvider>
					</v-col>
				</v-row>
				<v-row v-if="shipping.method !== 'retail'">
					<v-col offset-lg="3" :cols="6" :lg="6" v-if="shipping.method === 'pickup'" class="pa-1">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="From">
							<v-select
								:error-messages="errors"
								:success="valid"
								:loading="loading"
								label="From"
								:items="storePickups"
								item-text="name"
								v-model="shipping.pickup_point"
								prepend-icon="mdi-store"
								return-object
								@change="validate"
							></v-select>
						</ValidationProvider>
					</v-col>
					<v-col cols="6" lg="2" :offset-lg="3" class="pa-1" v-if="shipping.method === 'delivery'">
						<v-select
							:loading="loading"
							label="Occasion"
							:items="occasions"
							item-text="label"
							item-value="id"
							v-model="shipping.occasion"
							prepend-icon="mdi-star-face"
						></v-select>
					</v-col>
					<v-col offset-lg="2" cols="6" lg="2" v-if="shipping.method === 'delivery'" class="pa-1">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Location">
							<v-select
								:error-messages="errors"
								:success="valid"
								:loading="loading"
								label="Location"
								:items="locations"
								item-text="label"
								item-value="id"
								v-model="shipping.location"
								prepend-icon="mdi-city"
								@change="validate"
							></v-select>
						</ValidationProvider>
					</v-col>
				</v-row>
				<v-row>
					<v-col cols="12" lg="6" offset-lg="3" class="pa-2">
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
			</v-form>
		</ValidationObserver>
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
			datePicker: false,
			store_pickups: [],

			dialog: {
				show: false,
				width: 600,
				fullscreen: false,
				title: "",
				titleCloseBtn: false,
				icon: "",
				component: "",
				content: "",
				model: "",
				persistent: false
			}
		};
    },
    watch: {
        shippingMethod() {
            this.validate();
        }
    },

	computed: {
        shippingMethod() {
            return this.shipping.method;
        },
		storePickups: {
			get() {
				return this.store_pickups;
			},
			set(value) {
				this.store_pickups = value;
			}
		},
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
		dateFormatted() {
			if (this.$store.state.cart.shipping.date) {
				return this.parseDate(this.$store.state.cart.shipping.date);
			} else {
				return null;
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
		billingAddress: {
			get() {
				return this.$store.state.cart.billing_address;
			},
			set(value) {
				this.$store.state.cart.billing_address = value;
			}
		},
		shippingCost: {
			get() {
				return this.$store.state.cart.shipping.cost;
			},
			set(value) {
				this.$store.commit("cart/setShippingCost", value);
			}
		},

		addresses: {
			get() {
				if (this.customer.id && !this.billingAddress) {
					const billing_address = _.filter(this.customer.addresses, [
						"billing",
						1
					]);

					this.billingAddress = billing_address.length
						? billing_address[0]
						: null;
                }
                if (this.customer.id && !this.shippingAddress) {
					const shipping_address = _.filter(this.customer.addresses, [
						"shipping",
						1
					]);

					this.shippingAddress = shipping_address.length
						? shipping_address[0]
						: null;
				}

				return this.customer.addresses;
			},
			set(value) {
				this.customer.addresses = value;
			}
		},
		customer() {
			if (this.$store.state.cart.order.id) {
				return this.$store.state.cart.order.customer;
			} else if (this.$store.state.cart.customer) {
				return this.$store.state.cart.customer;
			} else {
				return null;
			}
		}
	},

	mounted() {
		this.getStores();
		this.validate();
	},

	methods: {
		validate() {
			if (this.shipping.method === "retail") {
				this.$store.state.cart.isValid = true;
			} else {
				this.$nextTick(() => {
					this.$refs.shippingValidation.validate().then(result => {
						this.$store.state.cart.isValid = result;
					});
				});
			}
		},
		parseDate(d) {
			if (d.length) {
				let [year, month, day] = d.split("-");
				return `${month}/${day}/${year}`;
			} else {
				return null;
			}
		},
		dialogEvent(event) {
			if (event) {
				this.addresses.push(event);

				if (this.dialog.model.type === "shipping") {
					this.shipping.address = event;
				} else {
					this.billingAddress = event;
				}
			}

			this.resetDialog();
		},
		addressDialog(action, address) {
			let icon;
			let title;
			let type;

			if (action === "delivery") {
				type = "shipping";
				icon = "mdi-map-marker";
				title = address ? "Edit delivery address" : "New delivery address";
			} else {
				type = "billing";
				icon = "mdi-receipt";
				title = address ? "Edit billing address" : "New billing address";
			}

			address ? (address.type = type) : null;

			this.dialog = {
				show: true,
				width: 600,
				fullscreen: false,
				title: title,
				titleCloseBtn: true,
				icon: icon,
				component: "addressDeliveryForm",
				content: "",
				model: address || { type },
				persistent: true
			};
		},
		resetDialog() {
			this.dialog = {
				show: false,
				width: 600,
				fullscreen: false,
				title: "",
				titleCloseBtn: false,
				icon: "",
				component: "",
				content: "",
				model: "",
				persistent: false
			};
		},
		getStores() {
			this.getAll({ model: "store-pickups" }).then(response => {
				this.storePickups = response;
			});
		},
		closedDialog(event) {
			if (event && event !== true) {
				this.timeSlots.push(event);
				this.shipping.timeSlotLabel = event.label;
			}

			this.addTimeSlotDialog = false;
		},
		setCost(item) {
			if (_.has(item, "cost")) {
				this.shippingCost = item.cost;
			} else {
				this.shippingCost = null;
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
				item.address_region.default_name +
				", " +
				item.postcode +
				", " +
				item.address_country
			);
		},
		...mapActions(["postRequest", "getAll"])
	}
};
</script>
