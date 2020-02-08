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
					<v-textarea
						outlined
						label="Σκονάκι"
						v-model="sdkSkonaki"
						readonly
						:loading="loading"
						v-if="endpoint === 'SDK'"
					></v-textarea>
					<v-row v-if="endpoint === 'API'">
						<v-col :cols="6">
							<v-textarea
								outlined
								label="Test cards"
								v-model="testCards"
								readonly
								:loading="loading"
								auto-grow
							></v-textarea>
						</v-col>
						<v-col :cols="6">
							<v-textarea
								outlined
								label="CCV2/CVC2 RESPONSE CODE"
								v-model="apiSkonaki"
								readonly
								:loading="loading"
								auto-grow
							></v-textarea>
						</v-col>
					</v-row>
					<v-btn type="submit" :loading="loading" :disabled="loading" color="tertiary">submit</v-btn>
					<v-btn
						@click="getSdkLogs"
						v-if="endpoint === 'SDK'"
						color="info"
						:loading="loading"
						:disabled="loading"
					>get logs</v-btn>
					<v-btn
						@click="getApiLogs"
						v-if="endpoint === 'API'"
						color="info"
						:loading="loading"
						:disabled="loading"
					>get logs</v-btn>
					<v-btn
						@click="deleteSdkLogs"
						v-if="endpoint === 'SDK'"
						color="error"
						:loading="loading"
						:disabled="loading"
					>delete logs</v-btn>
					<v-btn
						@click="deleteApiLogs"
						v-if="endpoint === 'API'"
						color="error"
						:loading="loading"
						:disabled="loading"
					>delete logs</v-btn>

					<v-row v-if="endpoint === 'SDK'">
						<v-col :cols="1">
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
						<v-switch
							class="mx-2"
							v-if="sdk.selected_transaction === 'PRE_AUTH_COMPLETE' || sdk.selected_transaction === 'PRE_AUTH' || sdk.selected_transaction === 'SALE'"
							:disabled="loading"
							:loading="loading"
							v-model="sdk.taxFree"
							:label="'Tax free'"
						></v-switch>
						<v-switch
							class="mx-2"
							v-if="sdk.selected_transaction === 'PRE_AUTH_COMPLETE' || sdk.selected_transaction === 'PRE_AUTH' || sdk.selected_transaction === 'SALE'"
							:disabled="loading"
							:loading="loading"
							v-model="sdk.voiceReferral"
							:label="'Voice Referral'"
						></v-switch>
						<v-switch
							class="mx-2"
							v-if="sdk.selected_transaction === 'PRE_AUTH_COMPLETE' || sdk.selected_transaction === 'PRE_AUTH' || sdk.selected_transaction === 'SALE'"
							:disabled="loading"
							:loading="loading"
							v-model="sdk.keyed"
							:label="'Keyed'"
						></v-switch>
						<v-col :cols="3" v-if="sdk.keyed">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdk.CARDHOLDER_ADDRESS"
								label="AVS - Cardholder address"
							></v-text-field>
						</v-col>
						<v-col :cols="3" v-if="sdk.keyed">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdk.CARDHOLDER_ZIP"
								label="AVS - Cardholder ZIP"
							></v-text-field>
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
						<v-col
							:cols="3"
							v-if="sdk.selected_transaction === 'PRE_AUTH_COMPLETE' || sdk.selected_transaction === 'SALE' || sdk.selected_transaction === 'LINKED_REFUND' || sdk.selected_transaction === 'PRE_AUTH'"
						>
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdk.invoiceNumber"
								label="Invoice number"
							></v-text-field>
						</v-col>
						<v-col :cols="3" v-if="sdk.selected_transaction === 'Transaction Lookup'">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdkLookup.transId"
								label="Transaction ID"
								hint="This parameter must be combined with UTC Date"
							></v-text-field>
						</v-col>
						<v-col :cols="3" v-if="sdk.selected_transaction === 'Transaction Lookup'">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdkLookup.creditCard"
								label="Credit card"
								hint="You can combine this parameter with beginDate and/or endDate"
							></v-text-field>
						</v-col>
						<v-col :cols="1" v-if="sdk.selected_transaction === 'Transaction Lookup'">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdkLookup.last4CC"
								label="Last four cc"
								hint="You can combine this parameter with beginDate and/or endDate"
							></v-text-field>
						</v-col>
						<v-col :cols="2" v-if="sdk.selected_transaction === 'Transaction Lookup'">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdkLookup.beginDate"
								label="Begin date"
								hint="Format: YYYYMMDD. Important: The date range must not exceed 31 days. You can combine this parameter with creditCard or last4CC"
							></v-text-field>
						</v-col>
						<v-col :cols="2" v-if="sdk.selected_transaction === 'Transaction Lookup'">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdkLookup.endDate"
								label="End date"
								hint="Format: YYYYMMDD. Important: The date range must not exceed 31 days. You can combine this parameter with creditCard or last4CC"
							></v-text-field>
						</v-col>
						<v-col :cols="2" v-if="sdk.selected_transaction === 'Transaction Lookup'">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdkLookup.utcDate"
								label="UTC Date"
								hint="Format: yyyy/MM/dd hh/mm/ss Important: This parameter must be supplied if Transaction ID is also included."
							></v-text-field>
						</v-col>
						<v-col :cols="2" v-if="sdk.selected_transaction === 'Transaction Lookup'">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="sdkLookup.merchantTransactionReference"
								label="Merchant Transaction Reference"
								hint="This parameter must be combined with one or more of begin Date, End Date, last4CC, or Credit Card"
							></v-text-field>
						</v-col>
						<v-col :cols="12">
							<v-data-table :items="sdkLogs" :headers="sdkHeaders" @click:row="openLog" :loading="loading"></v-data-table>
						</v-col>
					</v-row>

					<v-row v-else>
						<v-col :cols="1">
							<v-text-field
								v-model="api.test_case"
								:disabled="loading"
								:loading="loading"
								label="Test case"
							></v-text-field>
						</v-col>
						<v-col :cols="2">
							<v-text-field
								:disabled="loading"
								:loading="loading"
								v-model="api.ssl_card_number"
								label="Card number"
							></v-text-field>
						</v-col>
						<v-col :cols="2">
							<v-text-field :disabled="loading" :loading="loading" v-model="api.ssl_cvv2cvc2" label="CVC"></v-text-field>
						</v-col>
						<v-col :cols="2">
							<v-text-field
								type="number"
								:disabled="loading"
								:loading="loading"
								v-model="api.ssl_amount"
								label="Amount"
							></v-text-field>
						</v-col>
						<v-col :cols="2">
							<v-select
								:disabled="loading"
								:loading="loading"
								:items="apiCvcs"
								item-text="label"
								item-value="value"
								v-model="api.ssl_cvv2cvc2_indicator"
								label="Card Validation Code Indicator"
							></v-select>
						</v-col>
						<v-col :cols="12">
							<v-data-table :items="apiLogs" :headers="apiHeaders" @click:row="openLog" :loading="loading"></v-data-table>
						</v-col>
					</v-row>
				</v-card-text>
			</v-card>
		</v-form>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			apiSkonaki:
				"M	CVV2/CVC2 Match\nN	CVV2/CVC2 No match\nP	Not processed\nS	Issuer indicates that the CVV2/CVC2 data should be present on the card, but the merchant has indicated that the CVV2/CVC2 data is not resent on the card\nU	Issuer has not certified for CVV2/CVC2 or Issuer has not provided Visa with the CVV2/CVC2 encryption keys",
			sdkSkonaki:
				"SALE: sale\nPRE_AUTH: Auth Only\nPRE_AUTH_COMPLETE: Convert Auth Only to Sale\nPRE_AUTH_DELETE: Auth Only Reversal\nVOID: Void\nLINKED_REFUND: Linked Refund\nSTANDALONE_REFUND: Stand Alone Refund\n",
			testCards:
				"Visa (Also works for 3D Secure)	4000000000000002\nVisa Corporate (Allows for the capture of additional Level 2 Data)	4159288888888882\nMasterCard	5121212121212124\nDiscover	6011000000000004\nDiners Club	36111111111111 (14 digit), 3811112222222222 (16 digit)\nAmerican Express	370000000000002\nJCB	3566664444444445\nElectronic Gift Card (EGC)	6032610007325520\nForeign Currency Cards	4032769999999992 (CAD), 5432675555555552 (EUR)",
			loading: false,
			endpoint: "SDK",
			sdk: {
				invoiceNumber: null,
				taxFree: false,
				keyed: false,
				voiceReferral: false,
				originalTransId: "",
				test_case: "",
				transaction_types: [
					"SALE",
					"PRE_AUTH",
					"PRE_AUTH_COMPLETE",
					"PRE_AUTH_DELETE",
					"VOID",
					"LINKED_REFUND",
					"STANDALONE_REFUND",
					"Transaction Lookup"
				],
				amount: null,
				selected_transaction: null,
				CARDHOLDER_ADDRESS: null,
				CARDHOLDER_ZIP: null
			},
			sdkLookup: {
				creditCard: null,
				last4CC: null,
				beginDate: null,
				endDate: null,
				transId: null,
				utcDate: null,
				merchantTransactionReference: null
			},
			sdkHeaders: [
				{ text: "#", value: "id" },
				{ text: "Test case", value: "test_case" },
				{ text: "Payment gateway id", value: "payment_gateway_id" },
				{ text: "Chan id", value: "chan_id" },
				{ text: "Status", value: "status" },
				{ text: "Log", value: "log" }
			],
			apiHeaders: [
				{ text: "#", value: "id" },
				{ text: "Test case", value: "test_case" },
				{ text: "Transaction Id", value: "txn_id" },
				{ text: "Status", value: "status" },
				{ text: "Log", value: "log" }
			],
			sdkLogs: [],
			apiLogs: [],
			showLog: false,
			item: {},
			apiCvcs: [
				{ label: "Bypassed", value: 0 },
				{ label: "Present", value: 1 },
				{ label: "Illegible", value: 2 },
				{ label: "Not Present", value: 9 }
			],
			api: {
				ssl_card_number: null,
				ssl_amount: null,
				ssl_cvv2cvc2_indicator: null,
				ssl_cvv2cvc2: null
			}
		};
	},

	methods: {
		openLog(item) {
			this.item = item;
			this.showLog = true;
		},
		getSdkLogs() {
			this.loading = true;

			let url = "/api/elavon/sdk/logs";

			this.sdk.test_case ? (url += "/" + this.sdk.test_case) : null;
			axios
				.get(url)
				.then(response => {
					this.sdkLogs = response.data;
				})
				.finally(() => {
					this.loading = false;
				});
		},
		getApiLogs() {
			this.loading = true;

			let url = "/api/elavon/api/logs";

			this.api.test_case ? (url += "/" + this.api.test_case) : null;
			axios
				.get(url)
				.then(response => {
					this.apiLogs = response.data;
				})
				.finally(() => {
					this.loading = false;
				});
		},
		deleteSdkLogs() {
			this.loading = true;
			axios
				.delete("/api/elavon/sdk/logs/delete")
				.then(() => {
					this.sdkLogs = [];
				})
				.finally(() => {
					this.loading = false;
				});
		},
		deleteApiLogs() {
			this.loading = true;
			axios
				.delete("/api/elavon/api/logs/delete")
				.then(() => {
					this.apiLogs = [];
				})
				.finally(() => {
					this.loading = false;
				});
		},
		submit() {
			this.loading = true;
			if (this.endpoint === "SDK") {
				if (this.sdk.selected_transaction === "Transaction Lookup") {
					axios
						.post("/api/elavon/sdk/lookup", this.sdkLookup)
						.catch(error => {
							alert(
								"Status code: " +
									error.response.status +
									"\nMessage: " +
									error.response.data.message
							);

							this.loading = false;
						})
						.finally(() => {
							this.getSdkLogs();
							this.loading = false;
						});
				} else {
					axios
						.post("/api/elavon/sdk", this.sdk)
						.catch(error => {
							alert(
								"Status code: " +
									error.response.status +
									"\nMessage: " +
									error.response.data.message
							);

							this.loading = false;
						})
						.finally(() => {
							this.getSdkLogs();
							this.loading = false;
						});
				}
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

						this.loading = false;
					})
					.finally(() => {
						this.getApiLogs();
						this.loading = false;
					});
			}
		}
	}
};
</script>
