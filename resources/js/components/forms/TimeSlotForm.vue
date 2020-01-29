<template>
	<v-form @submit.prevent="submit()">
		<v-row>
			<v-col :cols="6">
				<v-select label="From" :items="hours" v-model="timeSlot.from_h"></v-select>
			</v-col>
			<v-col :cols="6">
				<v-select label="To" :items="hours" v-model="timeSlot.to_h"></v-select>
			</v-col>
			<v-col :cols="12">
				<v-spacer></v-spacer>
				<v-btn type="submit" :disabled="!(timeSlot.from_h && timeSlot.to_h)">OK</v-btn>
			</v-col>
		</v-row>
	</v-form>
</template>

<script>
export default {
	data() {
		return {
			timeSlot: {
				from_h: null,
				to_h: null
			}
		};
	},

	computed: {
		hours() {
			let a = Array.from(Array(48).keys()).map((value, i) => {
				const flag = i % 2 == 0;

				if (i > 19) {
					return parseInt(i / 2) + (flag ? ":00" : ":30");
				} else {
					return "0" + parseInt(i / 2) + (flag ? ":00" : ":30");
				}
			});

			return a;
		},
		hoursAmPm() {
			let x = 30; //minutes interval
			let times = []; // time array
			let tt = 0; // start time
			let ap = [" AM", " PM"]; // AM-PM

			//loop to increment the time and push results in array
			for (let i = 0; tt < 24 * 60; i++) {
				let hh = Math.floor(tt / 60); // getting hours of day in 0-24 format
				let mm = tt % 60; // getting minutes of the hour in 0-55 format
				times[i] =
					("" + (hh == 12 ? 12 : hh % 12)).slice(-2) +
					":" +
					("0" + mm).slice(-2) +
					ap[Math.floor(hh / 12)]; // pushing data in array in [00:00 - 12:00 AM/PM format]
				tt = tt + x;
			}
			return times;
		}
	},

	methods: {
		submit() {
			let payload = {
				notification: false,
				data: {
					label: this.timeSlot.from_h + "-" + this.timeSlot.to_h
				}
			};

			this.$emit("submit", payload);
		}
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>