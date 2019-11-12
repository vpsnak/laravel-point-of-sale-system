<template>
	<v-container v-if="cashRegisterData">
		<v-row>
			<v-col cols="12">
				<v-card>
					<v-card-title>{{cashRegisterData.name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">Created at: {{ cashRegisterData.created_at }}</div>
						<div class="subtitle-1">Updated at: {{ cashRegisterData.updated_at }}</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="cashRegisterData.logs">
				<v-card>
					<v-card-title>Logs</v-card-title>
					<v-card-text>
						<v-simple-table dense>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">Opening amount</th>
										<th class="text-left">Closing amount</th>
										<th class="text-left">Status</th>
										<th class="text-left">Opening time</th>
										<th class="text-left">Closing time</th>
										<th class="text-left">Opened by</th>
										<th class="text-left">Closed by</th>
										<th class="text-left">note</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="log in cashRegisterData.logs" :key="log.id">
										<td>{{ log.opening_amount }}</td>
										<td>{{ log.closing_amount }}</td>
										<td>{{ log.status }}</td>
										<td>{{ log.opening_time }}</td>
										<td>{{ log.closing_time }}</td>
										<td>{{ log.opened_by }}</td>
										<td>{{ log.closed_by }}</td>
										<td>{{ log.note }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-else>
				<v-card-title>Logs</v-card-title>
				<v-card-text>There are no logs for this this cash register</v-card-text>
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
			cashRegister: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "cash-registers",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.cashRegister = result;
			});
	},
	computed: {
		cashRegisterData() {
			return this.cashRegister;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
