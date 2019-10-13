<template>
	<v-container>
		<v-btn @click.stop="showConfirmationDialog = true">Confirmation dialog</v-btn>

		<interactiveDialog
			v-if="showConfirmationDialog"
			action="confirmation"
			:show="showConfirmationDialog"
			title="Confirmation dialog"
			content="Confirmation dialog <h2><s>html</s></h2> content"
			@action="confirmationResult"
			persistent
			cancelBtnTxt="No"
			confirmationBtnTxt="Yes"
		/>

		<v-btn @click.stop="showInfoDialog = true">Update dialog</v-btn>

		<interactiveDialog
			v-if="showInfoDialog"
			action="update"
			:show="showInfoDialog"
			title="Update dialog"
			component="paymentActions"
			@action="editDialog"
		/>

		<v-btn @click.stop="showReadDialog = true">Read dialog</v-btn>

		<interactiveDialog
			v-if="showReadDialog"
			action="read"
			:show="showReadDialog"
			title="Read dialog"
			component="categoryForm"
			:model="category"
			@action="readResult"
			cancelBtnTxt="Close"
		/>

		<v-btn @click.stop="showEditDialog = true">Update dialog</v-btn>

		<interactiveDialog
			v-if="showEditDialog"
			action="update"
			:show="showEditDialog"
			title="Update dialog"
			component="paymentActions"
			@action="editDialog"
		/>

		<v-sparkline
			:fill="fill"
			:gradient="gradient"
			:line-width="width"
			:padding="padding"
			:smooth="radius || false"
			:value="value"
			auto-draw
		></v-sparkline>

		<v-divider></v-divider>

		<v-row>
			<v-col cols="12" md="6">
				<v-row class="fill-height" align="center">
					<v-item-group v-model="gradient" mandatory>
						<v-row>
							<v-item
								class="ml-5"
								v-for="(gradient, i) in gradients"
								:key="i"
								v-slot:default="{ active, toggle }"
								:value="gradient"
							>
								<v-card
									:style="{
                    background: gradient.length > 1
                      ? `linear-gradient(0deg, ${gradient})`
                      : gradient[0],
                    border: '2px solid',
                    borderColor: active ? '#222' : 'white'
                  }"
									width="30"
									height="30"
									class="mr-2"
									@click.native="toggle"
								></v-card>
							</v-item>
						</v-row>
					</v-item-group>
				</v-row>
			</v-col>

			<v-col cols="12" md="6">
				<v-slider v-model="width" label="Width" min="0.1" max="10" step="0.1" thumb-label></v-slider>
			</v-col>

			<v-col cols="6">
				<v-row class="fill-height" align="center">
					<v-switch v-model="fill" label="Filled"></v-switch>
				</v-row>
			</v-col>

			<v-col cols="12" md="6">
				<v-slider v-model="radius" label="Radius" min="0" max="25" thumb-label></v-slider>
			</v-col>

			<v-col cols="12" md="6" offset-md="6">
				<v-slider v-model="padding" label="Padding" min="0" max="25" thumb-label></v-slider>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
const gradients = [
	["#222"],
	["#42b3f4"],
	["red", "orange", "yellow"],
	["purple", "violet"],
	["#00c6ff", "#F0F", "#FF0"],
	["#f72047", "#ffd200", "#1feaea"]
];
export default {
	data() {
		return {
			showConfirmationDialog: false,
			showReadDialog: false,
			showEditDialog: false,
			showInfoDialog: false,
			fill: true,
			gradient: gradients[4],
			gradients,
			padding: 8,
			radius: 10,
			value: [0, 2, 5, 9, 5, 10, 3, 5, 0, 0, 1, 8, 2, 9, 0],
			width: 2,

			category: {
				name: "Category 1",
				in_product_listing: true
			}
		};
	},

	methods: {
		confirmationResult(event) {
			console.log("confirmation dialog result: " + event);
			this.showConfirmationDialog = false;
		},
		readResult(event) {
			console.log("read dialog result: " + event);
			this.showReadDialog = false;
		},
		editDialog(event) {
			console.log("update dialog result: " + event);
			this.showEditDialog = false;
		}
	}
};
</script>