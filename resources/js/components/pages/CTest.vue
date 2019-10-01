<template>
	<v-container fluid>
		<v-row>
			<v-col align="center" cols="6" justify="center" offset="2">
				<!-- <payment totalAmount="359.46" /> -->
				<address :address="customer.addresses[0]"></address>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			customer: {}
		};
	},
	mounted() {
		let payload = {
			model: "customers",
			data: { id: 1 }
		};

		this.$store.dispatch("getOne", payload).then(response => {
			this.customer = response;
			console.log(this.customer.addresses[0]);

			console.log(
				this.customer.addresses[0].street +
					" " +
					this.customer.addresses[0].city +
					", " +
					this.customer.addresses[0].area_code_id +
					" "
			);
		});
	}
};
</script>
