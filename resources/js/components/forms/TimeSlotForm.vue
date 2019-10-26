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