<template>
	<v-dialog v-model="show" :persistent="persistent" :max-width="maxWidth" :fullscreen="fullscreen">
		<v-card>
			<v-card-title class="headline">
				{{ title }}
				<v-spacer></v-spacer>
				<v-btn @click="cancel" icon>
					<v-icon>mdi-close</v-icon>
				</v-btn>
			</v-card-title>

			<v-divider></v-divider>

			<v-card-text>
				<component :is="content"></component>
			</v-card-text>

			<v-card-actions>
				<div class="flex-grow-1"></div>

				<v-btn @click="cancel" text color="error">{{ cancelBtn }}</v-btn>

				<v-btn @click="ok" text color="success">{{ confirmationBtn }}</v-btn>
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
		content: String,
		fullscreen: Boolean,
		cancelBtnTxt: String || "",
		confirmationBtnTxt: String || ""
	},

	computed: {
		show: {
			get() {
				return this.$store.state.confirmationDialog;
			},
			set(value) {
				this.$store.state.confirmationDialog = value;
			}
		},
		maxWidth() {
			return this.$props.width || 600;
		},
		cancelBtn() {
			return this.$props.cancelBtnTxt || "Cancel";
		},
		confirmationBtn() {
			return this.$props.confirmationBtnTxt || "OK";
		}
	},

	methods: {
		cancel() {
			this.$emit("confirmation", false);
		},
		ok() {
			this.$emit("confirmation", true);
		}
	},

	beforeDestroy() {
		this.$off("confirmation");
	}
};
</script>