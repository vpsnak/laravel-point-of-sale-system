<template>
	<div>
		<ValidationObserver ref="shippingValidation">
			<v-form>
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
export default {
	mounted() {
		this.validate();
	},
	computed: {
		shipping: {
			get() {
				return this.$store.state.cart.shipping;
			},
			set(value) {
				this.$store.state.cart.shipping = value;
			}
		}
	},
	methods: {
		validate() {
			this.$store.state.cart.isValid = true;
		}
	}
};
</script>
