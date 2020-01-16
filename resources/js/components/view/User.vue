<template>
	<v-container>
		<v-row v-if="userData">
			<v-col cols="12">
				<v-card>
					<v-card-title>{{userData.name}}</v-card-title>
					<v-card-text>
						<div class="subtitle-1">
							Email:
							<a :href="'mailto:' + userData.email">{{userData.email}}</a>
						</div>
						<div class="subtitle-1">Email verified at: {{userData.email_verified_at}}</div>
						<div class="subtitle-1">
							Phone:
							<a :href="'tel:' + userData.phone">{{userData.phone}}</a>
						</div>
						<div class="subtitle-1">Created at: {{userData.created_at}}</div>
						<div class="subtitle-1">Updated at: {{userData.updated_at}}</div>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="userData.open_register">
				<v-card>
					<v-card-title>Open Register</v-card-title>
					<v-card-text>
						<v-simple-table dense>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">Cash Register</th>
										<th class="text-left">Opening amount</th>
										<th class="text-left">Status</th>
										<th class="text-left">Opening time</th>
										<th class="text-left">Created at</th>
										<th class="text-left">Updated at</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{opened_cash_register }}</td>
										<td>{{ userData.open_register.opening_amount }}</td>
										<td>{{ userData.open_register.status }}</td>
										<td>{{ userData.open_register.opening_time }}</td>
										<td>{{ userData.open_register.created_at }}</td>
										<td>{{ userData.open_register.updated_at }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="8" v-else>
				<v-card-title>Open registers</v-card-title>
				<v-card-text>There are no open register for {{userData.name}}</v-card-text>
			</v-col>
			<v-col cols="12" v-if="userData.roles">
				<v-card>
					<v-card-title>{{userData.name}} Roles</v-card-title>
					<v-card-text>
						<v-simple-table dense>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">Name</th>
										<th class="text-left">Created at</th>
										<th class="text-left">Updated at</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="role in userData.roles" :key="role.id">
										<td>{{ role.name }}</td>
										<td>{{ role.created_at }}</td>
										<td>{{ role.updated_at }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="8" v-else>
				<v-card-title>User Roles</v-card-title>
				<v-card-text>There are no roles for {{userData.name}}</v-card-text>
			</v-col>
		</v-row>
		<v-row v-else>
			<v-col cols="12" align="center" justify="center">
				<v-progress-circular indeterminate color="secondary"></v-progress-circular>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Int32Array | null
	},
	data() {
		return {
			user: null,
			opened_cash_register: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "users",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.user = result;
				if (this.userData.open_register) {
					this.getOne({
						model: "cash-registers",
						data: {
							id: this.userData.open_register.cash_register_id
						}
					}).then(response => {
						this.opened_cash_register = response.name;
					});
				}
			});
	},
	computed: {
		userData() {
			return this.user;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
