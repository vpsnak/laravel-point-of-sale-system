<template>
	<div>
		<div class="d-flex justify-center align-center">
			<v-radio-group v-model="shipping.method" @change="selectedShippingMethod" row>
				<v-radio label="In store pickup" value="pickup"></v-radio>
				<v-radio label="Shipping" value="shipping"></v-radio>
			</v-radio-group>
		</div>
		<v-row v-if="shipping.method === 'shipping'">
			<v-col cols="6">
				<v-menu
					v-model="datePicker"
					:close-on-content-click="false"
					:nudge-right="40"
					transition="scale-transition"
					offset-y
					min-width="290px"
				>
					<template v-slot:activator="{ on }">
						<v-text-field v-model="shipping.date" label="Date" prepend-icon="event" v-on="on"></v-text-field>
					</template>
					<v-date-picker v-model="shipping.date" @input="datePicker = false"></v-date-picker>
				</v-menu>
			</v-col>
			<v-col cols="6">
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
						<v-text-field v-model="shipping.time" label="At" prepend-icon="access_time" v-on="on"></v-text-field>
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
	</div>
</template>

<script>
export default {
	data() {
		return {
			datePicker: false,
			timePicker: false,
			shipping: {
				method: undefined,
				date: undefined,
				time: undefined,
				cost: 15
			},
			items: ["Foo", "Bar", "Fizz", "Buzz"]
		};
	},
	methods: {
		selectedShippingMethod() {
			this.$emit("shipping", this.shipping);
		}
	},
	beforeDestroy() {
		this.$off("shipping");
	}
};
</script>