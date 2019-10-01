<template>
	<div>
		<v-dialog v-model="show" max-width="600px" transition="dialog-bottom-transition">
			<v-card>
				<v-card-title>
					<span class="headline">Form</span>
				</v-card-title>
				<v-card-text>
					<v-container>
						<v-row>
							<v-col>
								<component :is="form" />
							</v-col>
						</v-row>
					</v-container>
				</v-card-text>
			</v-card>
		</v-dialog>
	</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	computed: {},
	methods: {
		close() {}
	},
	props: {
		form: String,
		show: Boolean
	},
	submit() {
		let payload = {
			model: "products",
			data: {
				sku: this.sku,
				name: this.name,
				photo_url: this.photo_url
			}
		};
		this.create(payload).then(() => {});
	},
	clear() {
		this.sku = "";
		this.name = "";
		this.photo_url = "";
	}
};
</script>
