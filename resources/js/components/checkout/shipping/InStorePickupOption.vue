<template>
	<div>
		<ValidationObserver ref="inStorePickupValidation">
			<v-form>
				<v-row>
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
										@blur="parseDate, validate"
										readonly
									></v-text-field>
								</ValidationProvider>
							</template>
							<v-date-picker v-model="shipping.date" :min="new Date().toJSON()"></v-date-picker>
						</v-menu>
					</v-col>
					<v-col cols="4" lg="3" class="pa-1">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Time Slot">
							<v-select
								:loading="loading"
								label="Time Slot"
								prepend-icon="mdi-clock"
								append-outer-icon="mdi-plus"
								:items="timeSlots"
								:error-messages="errors"
								:success="valid"
								item-text="label"
								v-model="shipping.timeSlotLabel"
								@input="setCost"
								@click:append-outer="addTimeSlotDialog()"
								@change="validate"
							></v-select>
						</ValidationProvider>
					</v-col>
					<v-col cols="4" lg="1" class="pa-1">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Fees">
							<v-text-field
								type="number"
								label="Fees"
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
				<v-row>
					<v-col offset-lg="3" :cols="6" :lg="6" v-if="shipping.method === 'pickup'" class="pa-1">
						<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Location">
							<v-select
								:error-messages="errors"
								:success="valid"
								:loading="loading"
								label="Location"
								:items="storePickups"
								item-text="name"
								v-model="shipping.pickup_point"
								prepend-icon="mdi-store"
								return-object
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
import { mapActions, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
	mounted() {
		this.getStores();
		this.validate();

		EventBus.$on("shipping-timeslot", event => {
			if (event.payload) {
				this.timeSlots.push(event.payload);
				this.shipping.timeSlotLabel = event.payload.label;
			}
		});
	},

	beforeDestroy() {
		EventBus.$off();
	},

	data() {
		return {
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
		validateOption() {
			this.validate();
		}
	},
	computed: {
		shipping: {
			get() {
				return this.$store.state.cart.shipping;
			},
			set(value) {
				this.$store.state.cart.shipping = value;
			}
		},
		storePickups: {
			get() {
				return this.store_pickups;
			},
			set(value) {
				this.store_pickups = value;
			}
		},
		timeSlots: {
			get() {
				return this.time_slots;
			},
			set(value) {
				this.time_slots = value;
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
		dateFormatted() {
			if (this.$store.state.cart.shipping.date) {
				return this.parseDate(this.$store.state.cart.shipping.date);
			} else {
				return null;
			}
		}
	},

	methods: {
		...mapActions(["postRequest", "getAll"]),
		...mapMutations("dialog", ["setDialog"]),

		addTimeSlotDialog() {
			this.setDialog({
				show: true,
				title: "Add time slot",
				titleCloseBtn: true,
				icon: "mdi-clock",
				component: "timeSlotForm",
				persistent: true,
				eventChannel: "shipping-timeslot"
			});
		},
		validate() {
			this.$nextTick(() => {
				this.$refs.inStorePickupValidation.validate().then(result => {
					this.$store.state.cart.isValid = result;
				});
			});
		},
		parseDate(d) {
			if (d.length) {
				let [year, month, day] = d.split("-");
				return `${month}/${day}/${year}`;
			} else {
				return null;
			}
		},
		getStores() {
			this.getAll({ model: "store-pickups" }).then(response => {
				this.storePickups = response;
			});
		},
		setCost(item) {
			if (_.has(item, "cost")) {
				this.shippingCost = item.cost;
			} else {
				this.shippingCost = null;
			}
		}
	}
};
</script>
