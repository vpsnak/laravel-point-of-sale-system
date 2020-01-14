<template>
	<v-dialog
		v-model="visibility"
		:persistent="persistent"
		:max-width="maxWidth"
		:fullscreen="fullscreen"
		@close:outer.stop
		scrollable
	>
		<v-card>
			<v-card-title>
				<v-icon v-if="icon" class="pr-2">{{ icon }}</v-icon>
				{{ title }}
				<v-spacer v-if="titleCloseBtn"></v-spacer>
				<v-btn v-if="titleCloseBtn" @click.stop="closeEvent(false)" icon>
					<v-icon>mdi-close</v-icon>
				</v-btn>
			</v-card-title>

			<v-divider class="mb-3"></v-divider>

			<v-card-text>
				<component
					:is="component"
					v-if="component"
					:model="model"
					:readOnly="readOnly"
					@submit="submit"
				></component>
				<div v-else v-html="content" :class="contentClass || ''"></div>
			</v-card-text>

			<!-- confirmation actions -->
			<v-card-actions
				class="d-flex align-center mt-5"
				v-if="action === 'confirmation' || action === 'info'"
			>
				<div class="flex-grow-1"></div>

				<v-btn
					v-if="action === 'confirmation' "
					@click="closeEvent(false)"
					text
					color="error"
				>{{ cancelBtn }}</v-btn>

				<v-btn @click="closeEvent(true)" text color="success">{{ confirmationBtn }}</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
	props: {
		show: Boolean,
		persistent: Boolean,
		width: Number,
		icon: String,
		title: String,
		titleCloseBtn: Boolean,

		content: String,
		contentClass: String,
		component: String,
		fullscreen: Boolean,
		cancelBtnTxt: String,
		confirmationBtnTxt: String,

		model: Object,
		action: String
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
		},
		readOnly() {
			return this.$props.action === "read" ? true : false;
		}
	},

	methods: {
		...mapActions(["getAll"]),
		submit(payload) {
			if (!payload) {
				this.closeEvent(true);
			} else {
				if (payload.notification) {
					this.$store.commit("setNotification", {
						msg: payload.notification.msg,
						type: payload.notification.type
					});
				}

				if (payload.data) {
					this.closeEvent(payload.data);
				} else {
					this.closeEvent(true);
				}
			}
		},

		closeEvent(payload) {
			this.$emit("action", payload);
			this.visibility = false;
		}
	},

	beforeDestroy() {
		this.$off("action");
	}
};
</script>
