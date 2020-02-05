<template>
	<v-container class="fill-height" fluid>
		<v-row align="center" justify="center">
			<v-col cols="12" sm="8" md="4">
				<v-img
					contain
					src="https://www.plantshed.com//skin/frontend/plantshed/default/images/ps-logo.svg"
				></v-img>
			</v-col>
			<v-col cols="12" align="center" justify="center">
				<v-progress-circular :color="color" :size="150" :value="loadPercent" :width="20">
					<b>{{ loadPercent }} %</b>
				</v-progress-circular>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
export default {
	mounted() {
		this.init();
	},
	data() {
		return {
			color: "secondary",
			error_txt: ""
		};
	},
	computed: {
		...mapState("config", ["app_load"]),

		loadPercent: {
			get() {
				if (this.app_load > 100) {
					return 100;
				} else {
					return this.app_load;
				}
			},
			set(value) {
				this.addLoadPercent(value);
			}
		}
	},
	methods: {
		...mapMutations("config", ["addLoadPercent"]),
		...mapMutations("cart", ["resetState"]),
		...mapMutations("dialog", ["resetDialog"]),
		...mapActions(["retrieveCashRegister"]),

		init() {
			this.resetAppState();

			this.retrieveCashRegister()
				.then(() => {
					this.loadPercent = 50;
				})
				.catch(error => {
					this.setColor(true);
				});

			this.redirectToDashboard();
		},
		redirectToDashboard() {
			if (this.$route.name !== "dashboard") {
				this.$router.push({ name: "dashboard" });
			}

			this.loadPercent = 25;
		},
		resetAppState() {
			this.resetState();
			this.resetDialog();
			this.loadPercent = 25;
		},
		setError(error) {
			if (error) {
				this.color = "danger";
				this.error = error;
			}
		}
	}
};
</script>
