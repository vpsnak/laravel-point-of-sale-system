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
				<v-btn type="submit">OK</v-btn>
			</v-col>
		</v-row>
	</v-form>
</template>

<script>
export default {
	data() {
		return {
			hours: [
				"00",
				"01",
				"02",
				"03",
				"04",
				"05",
				"06",
				"07",
				"08",
				"09",
				"10",
				"11",
				"12",
				"13",
				"14",
				"15",
				"16",
				"17",
				"18",
				"19",
				"20",
				"21",
				"22",
				"23"
			],
			timeSlot: {
				from_h: null,
				to_h: null
			}
		};
	},
	methods: {
		submit() {
			let payload = {
				label: this.timeSlot.from_h + " - " + this.timeSlot.to_h,
				cost: null
			};

			this.$emit("addTimeSlot", payload);
		}
	},
	beforeDestroy() {
		this.$off("addTimeSlot");
	}
};
</script>