<template>
	<v-container v-if="productData">
		<v-row>
			<v-col cols="12" md="6">
				<v-card :img="productData.photo_url" height="100%"></v-card>
			</v-col>
			<v-col cols="12" md="6">
				<v-card>
					<v-card-title>{{productData.name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Sku: {{productData.sku}}</div>
						<div class="subtitle-1">Price: {{productData.final_price}}</div>
						<div class="subtitle-1">Stock: {{productData.stock}}</div>
						<div class="subtitle-1">Created at: {{productData.created_at}}</div>
						<div class="subtitle-1">Description: {{productData.description}}</div>
					</v-card-text>
					<v-simple-table>
						<template v-slot:default>
							<thead>
								<tr>
									<th class="text-left">Store name</th>
									<th class="text-left">Taxable</th>
									<th class="text-left">Is Default</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="store in productData.stores" :key="store.id">
									<td>{{ store.name }}</td>
									<td>{{ store.taxable }}</td>
									<td>{{ store.is_default }}</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
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
			product: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "products",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.product = result;
			});
	},
	computed: {
		productData() {
			return this.product;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
