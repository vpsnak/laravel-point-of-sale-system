<template>
	<v-dialog v-model="show" :persistent="persistent" :max-width="maxWidth" :fullscreen="fullscreen">
		<v-card>
			<v-card-title class="headline">
				{{ title }}
				<v-spacer></v-spacer>
				<v-btn @click="confirmation(false)" icon>
					<v-icon>mdi-close</v-icon>
				</v-btn>
			</v-card-title>

			<v-divider></v-divider>

			<v-card-text>
				<component :is="component" v-if="component"></component>
				<div v-else v-html="content" :class="'pt-5' + contentClass"></div>
			</v-card-text>

			<v-card-actions v-if="actions" class="d-flex align-center my-5">
				<div class="flex-grow-1"></div>

				<v-btn @click="confirmation(false)" text color="error">{{ cancelBtn }}</v-btn>

				<v-btn @click="confirmation(true)" text color="success">{{ confirmationBtn }}</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
export default {
	props: {
		persistent: Boolean || undefined,
		width: Number || undefined,
		title: String,
		actions: Boolean,
		content: String || undefined,
		contentClass: String || '',
		component: String,
		fullscreen: Boolean,
		cancelBtnTxt: String || "",
		confirmationBtnTxt: String || ""
	},

	computed: {
		show: {
			get() {
				return this.$store.state.interactiveDialog;
			},
			set(value) {
				this.$store.state.interactiveDialog = value;
			}
		},
		maxWidth() {
			return this.$props.width || 450;
		},
		cancelBtn() {
			return this.$props.cancelBtnTxt || "Cancel";
		},
		confirmationBtn() {
			return this.$props.confirmationBtnTxt || "OK";
		}
	},

	methods: {
		confirmation(confirmed) {
			this.$emit("confirmation", confirmed);

			this.show = false;
		},
	},

	beforeDestroy() {
		this.$off("confirmation");
	}
};
</script>