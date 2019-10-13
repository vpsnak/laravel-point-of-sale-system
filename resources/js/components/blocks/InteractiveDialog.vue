<template>
	<v-dialog
		v-model="visibility"
		:persistent="persistent"
		:max-width="maxWidth"
		:fullscreen="fullscreen"
		@click:outside="closeEvent"
		@keydown.esc="closeEvent"
	>
		<v-card>
			<v-card-title class="headline">
				{{ title }}
				<v-spacer v-if="titleCloseBtn"></v-spacer>
				<v-btn v-if="titleCloseBtn" @click.stop="fire(false)" icon>
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
					@addtocart="addtocart"
				></component>
				<div v-else v-html="content" :class="contentClass || ''"></div>
			</v-card-text>

			<!-- confirmation actions -->
			<v-card-actions
				class="d-flex align-center mt-5"
				v-if="action === 'confirmation' || action === 'info'"
			>
				<div class="flex-grow-1"></div>

				<v-btn v-if="action === 'confirmation' " @click="fire(false)" text color="error">{{ cancelBtn }}</v-btn>

				<v-btn @click="fire(true)" text color="success">{{ confirmationBtn }}</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
	props: {
		show: Boolean,
		persistent: Boolean || undefined,
		width: Number || undefined,
		title: String,
		titleCloseBtn: Boolean,

		content: String || undefined,
		contentClass: String || "",
		component: String,
		fullscreen: Boolean,
		cancelBtnTxt: String || "",
		confirmationBtnTxt: String || "",
		// model CRUD props
		model: Object,
		action: String // Possible values: edit, read, confirmation, info
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
		...mapActions("datatable", {
			getRows: "getRows",
			deleteRow: "deleteRow"
		}),

		submit(event) {
			this.submit = event;
			if (this.submit != null) {
				this.closeEvent();
			}

			this.getRows({
				url: this.submit
			});
			this.$store.commit("setNotification", {
				msg: "Saved in " + this.submit,
				type: "success"
			});
		},

		addtocart(event) {
			this.addtocart = event;
			if (this.addtocart != null) {
				this.closeEvent();
			}
			this.$store.commit("setNotification", {
				msg: "Added to " + this.addtocart,
				type: "success"
			});
		},

		fire(confirmation) {
			this.$emit("action", confirmation);
			this.visibility = false;
		},
		closeEvent() {
			this.$emit("action", false);
			this.visibility = false;
		}
	},

	beforeDestroy() {
		this.$off("action");
	}
};
</script>