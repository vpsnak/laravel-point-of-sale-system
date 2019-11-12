<template>
	<v-container v-if="categoryData">
		<v-row>
			<v-col cols="12">
				<v-card>
					<v-card-title>{{categoryData.name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Created at: {{categoryData.created_at}}</div>
						<div class="subtitle-1">Updated at: {{categoryData.updated_at}}</div>
						<div class="subtitle-1">In product listing: {{categoryData.in_product_listing ? 'Yes' : 'No'}}</div>
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
	<div v-else>Loading...</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Int32Array | null
	},
	data() {
		return {
			category: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "categories",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.category = result;
			});
	},
	computed: {
		categoryData() {
			return this.category;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
