<template>
	<v-container>
		<v-form @submit.prevent="submit">
			<v-card class="mx-auto pa-5">
				<v-card-text>
					<v-switch
						:disabled="loading"
						:loading="loading"
						v-model="endpoint"
						false-value="SDK"
						true-value="API"
						:label="'Selected method: ' + endpoint"
					></v-switch>
					<v-textarea outlined label="Σκονάκι" v-model="sdkSkonaki" readonly :loading="loading"></v-textarea>
					<v-btn type="submit" :loading="loading" :disabled="loading" color="success">submit</v-btn>
					<v-btn
						@click="getSdkLogs"
						v-if="endpoint==='SDK'"
						color="info"
						:loading="loading"
						:disabled="loading"
					>get logs</v-btn>
					<v-btn
						@click="deleteSdkLogs"
						v-if="endpoint==='SDK'"
						color="error"
						:loading="loading"
						:disabled="loading"
					>delete logs</v-btn>

					<v-row v-if="endpoint === 'SDK'">
						<v-col :cols="3">
							<v-text-field
								v-model="sdk.test_case"
								:disabled="loading"
								:loading="loading"
								label="Test case"
							></v-text-field>
						</v-col>
						<v-col :cols="3">
							<v-select
								:disabled="loading"
								:loading="loading"
								:items="sdk.transaction_types"
								v-model="sdk.selected_transaction"
								label="Transaction type"
							></v-select>
						</v-col>
						<v-col
							:cols="3"
							v-if="sdk.selected_transaction === 'SALE' || sdk.selected_transaction === 'PRE_AUTH' || sdk.selected_transaction === 'PRE_AUTH_COMPLETE' || sdk.selected_transaction === 'LINKED_REFUND' || sdk.selected_transaction === 'STANDALONE_REFUND'"
						>
							<v-text-field v-model="sdk.amount" :disabled="loading" :loading="loading" label="Amount"></v-text-field>
						</v-col>
						<v-col
							:cols="3"
							v-if="sdk.selected_transaction === 'PRE_AUTH_COMPLETE' || sdk.selected_transaction === 'PRE_AUTH_DELETE' || sdk.selected_transaction === 'VOID' || sdk.selected_transaction === 'LINKED_REFUND'"
						>
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdk.originalTransId"
								label="Original Trans ID"
							></v-text-field>
						</v-col>
						<v-col :cols="12" v-if="endpoint === 'SDK'">
							<v-data-table :items="sdkLogs" :headers="sdkHeaders" @click:row="openLog" :loading="loading"></v-data-table>
						</v-col>
					</v-row>

					<v-row v-else>
						<v-col :cols="3">
							<!-- <v-text-field v-model="asd"></v-text-field> -->
						</v-col>
					</v-row>
				</v-card-text>
			</v-card>
		</v-form>
		<interactiveDialog
			:width="600"
			:title="'Log #' + item.id"
			v-if="showLog"
			actions
			:show="showLog"
			@action="showLog = false"
			:content="'<pre><code style=\'max-width:100%\'>' + item.log + '</code></pre>'"
		></interactiveDialog>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			sdkSkonaki:
				"SALE: sale\nPRE_AUTH: Auth Only\nPRE_AUTH_COMPLETE: Convert Auth Only to Sale\nPRE_AUTH_DELETE: Auth Only Reversal\nVOID: Void\nLINKED_REFUND: Linked Refund\nSTANDALONE_REFUND: Stand Alone Refund\n",
			loading: false,
			endpoint: "SDK",
			sdk: {
				originalTransId: "",
				test_case: "",
				transaction_types: [
					"SALE",
					"PRE_AUTH",
					"PRE_AUTH_COMPLETE",
					"PRE_AUTH_DELETE",
					"VOID",
					"LINKED_REFUND",
					"STANDALONE_REFUND"
				],
				amount: null,
				selected_transaction: null
			},
			sdkHeaders: [
				{ text: "#", value: "id" },
				{ text: "Test case", value: "test_case" },
				{ text: "Payment gateway id", value: "payment_gateway_id" },
				{ text: "Chan id", value: "chan_id" },
				{ text: "Status", value: "status" },
				{ text: "Log", value: "log" }
			],
			sdkLogs: [],
			showLog: false,
			item: {},

			api: {}
		};
	},

	methods: {
		openLog(item) {
			this.item = item;
			this.showLog = true;
		},
		getSdkLogs() {
			this.loading = true;
			axios
				.get("/api/elavon/sdk/logs")
				.then(response => {
					this.sdkLogs = response.data;
				})
				.finally(() => {
					this.loading = false;
				});
		},
		deleteSdkLogs() {
			this.loading = true;
			axios
				.get("/api/elavon/sdk/logs/delete")
				.then(() => {
					this.sdkLogs = [];
				})
				.finally(() => {
					this.loading = false;
				});
		},
		submit() {
			this.loading = true;
			if (this.endpoint === "SDK") {
				axios
					.post("/api/elavon/sdk", this.sdk)
					.catch(error => {
						alert(
							"Status code: " +
								error.response.status +
								"\nMessage: " +
								error.response.data.message
						);
						console.log(error.response);
						this.loading = false;
					})
					.finally(() => {
						this.getSdkLogs();
						this.loading = false;
					});
			} else {
				axios
					.post("/api/elavon/api", this.api)
					.catch(error => {
						alert(
							"Status code: " +
								error.response.status +
								"\nMessage: " +
								error.response.data.message
						);
						console.log(error.response);
						this.loading = false;
					})
					.finally(() => {
						this.getSdkLogs();
						this.loading = false;
					});
			}
		}
	}
};
</script>