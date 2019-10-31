<template>
	<v-container>
		<v-form>
			<v-card class="mx-auto pa-5">
				<v-row>
					<v-col :cols="3">
						<v-switch
							:color="kwloterorita"
							v-model="endpoint"
							false-value="SDK"
							true-value="API"
							:label="'Selected method: ' + endpoint"
							@change="rainboW"
						></v-switch>
					</v-col>
					<v-col :cols="3">
						<v-text-field v-model="selected_transaction" label=""></v-text-field>
					</v-col>
					<v-col :cols="3">
						<v-text-field v-model="selected_transaction" label=""></v-text-field>
					</v-col>
					<v-col :cols="3">
						<v-text-field v-model="selected_transaction" label=""></v-text-field>
					</v-col>
				</v-row>
				<v-row v-if="endpoint === 'SDK'">
					<v-col :cols="3">
						<v-text-field v-model="asd"></v-text-field>
					</v-col>
				</v-row>
				<v-row v-else>
					<v-col :cols="3">
						<!-- <v-text-field v-model="asd"></v-text-field> -->
					</v-col>
				</v-row>
			</v-card>
		</v-form>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			endpoint: "SDK",
			transaction_types: ["SALE", "", "", ""],
			selected_transaction: null,
			asd: "",
			kwloterorita: "#FFFFFF"
		};
	},

	methods: {
		rainboW() {
			this.kwloterorita = "#" + ((Math.random() * 0xffffff) << 0).toString(16);
		}
	}
};
</script>