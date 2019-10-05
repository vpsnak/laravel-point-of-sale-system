<template>
	<v-dialog
		v-model="visibility"
		:persistent="persistent"
		:max-width="maxWidth"
		:fullscreen="fullscreen"
	>
		<v-card>
			<v-card-title class="headline">
				{{ title }}
				<v-spacer></v-spacer>
				<v-btn @click.stop="confirmation(false)" icon>
					<v-icon>mdi-close</v-icon>
				</v-btn>
			</v-card-title>

			<v-divider class="mb-3"></v-divider>

			<v-card-text>
				<component :is="component" v-if="component" :model="model"></component>
				<div v-else v-html="content" :class="contentClass || ''"></div>
			</v-card-text>

			<v-card-actions v-if="action === 'confirmation'" class="d-flex align-center mt-5">
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
			show: Boolean,
			persistent: Boolean || undefined,
			width: Number || undefined,
			title: String,

			content: String || undefined,
			contentClass: String || "",
			component: String,
			fullscreen: Boolean,
			cancelBtnTxt: String || "",
			confirmationBtnTxt: String || "",

			// model CRUD props
			model: Object,
			action: String // Possible values: create, read, update, delete, confirmation
		},

		data() {
			return {
				display: false
			};
		},

		mounted() {
			this.display = this.$props.show;
		},

		computed: {
			visibility: {
				get() {
					return this.display;
				},
				set(value) {
					this.display = value;
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

				this.visibility = false;
			}
		},

		beforeDestroy() {
			this.$off("confirmation");
		}
	};
</script>