<template>
	<ValidationObserver v-slot="{ invalid }" tag="v-form" @submit.prevent="submit">
		<v-row>
			<v-col cols="6">
				<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Coupon code">
					<v-text-field
						v-model="MASGiftcard.coupon_code"
						:readonly="$props.readonly"
						label="Coupon code"
						:disabled="loading"
						:error-messages="errors"
						:success="valid"
					></v-text-field>
				</ValidationProvider>
			</v-col>
			<v-col cols="6">
				<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Gift card code">
					<v-text-field
						:readonly="$props.readonly"
						v-model="MASGiftcard.giftcard_code"
						label="Gift card code"
						:disabled="loading"
						:error-messages="errors"
						:success="valid"
					></v-text-field>
				</ValidationProvider>
			</v-col>
		</v-row>
		<v-row v-if="!$props.readonly" justify="center">
			<v-btn
				color="success"
				type="submit"
				:disabled="invalid || loading"
				:loading="submit_loading"
			>save</v-btn>
		</v-row>
	</ValidationObserver>
</template>

<script>
export default {
	props: {
		model: Object,
		readonly: Boolean
	},
	data() {
		return {
			submit_loading: false,
			MASGiftcard: {
				coupon_code: null,
				giftcard_code: null
			}
		};
	},

	mounted() {
		if (this.$props.model) {
			this.MASGiftcard = { ...this.MASGiftcard, ...this.$props.model };
		}
	},
	computed: {
		loading() {
			if (this.submit_loading) {
				return true;
			} else {
				return false;
			}
		}
	},
	methods: {
		submit() {
			// TODO
			this.submit_loading = true;
			console.log(this.MASGiftcard);
		}
	}
};
</script>
