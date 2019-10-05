<template>
	<div>
		<v-form>
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-ticket-alt</v-icon>Coupon Form
				</v-chip>
			</div>
			<v-text-field v-model="name" label="Name" required></v-text-field>
			<v-text-field v-model="code" label="Code" required></v-text-field>
			<v-text-field v-model="uses" label="Uses" required></v-text-field>
			<v-text-field v-model="discount_id" type="number" label="Discount ID" required></v-text-field>
			<v-menu
				v-model="menu1"
				:close-on-content-click="false"
				:nudge-right="40"
				transition="scale-transition"
				offset-y
				min-width="290px"
			>
				<template v-slot:activator="{ on }">
					<v-text-field v-model="from" label="From:" prepend-icon="event" readonly v-on="on"></v-text-field>
				</template>
				<v-date-picker v-model="from" @input="menu1 = false"></v-date-picker>
			</v-menu>
			<v-menu
				v-model="menu2"
				:close-on-content-click="false"
				:nudge-right="40"
				transition="scale-transition"
				offset-y
				min-width="290px"
			>
				<template v-slot:activator="{ on }">
					<v-text-field v-model="to" label="To:" prepend-icon="event" readonly v-on="on"></v-text-field>
				</template>
				<v-date-picker v-model="to" @input="menu2 = false"></v-date-picker>
			</v-menu>

			<v-btn class="mr-4" @click="submit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
		<v-alert v-if="savingSuccessful === true" class="mt-4" type="success">Form submitted successfully!</v-alert>
	</div>
</template>
<script>
	import { mapActions } from "vuex";

	export default {
		data() {
			return {
				savingSuccessful: false,
				name: null,
				code: null,
				uses: null,
				discount_id: null,
				from: null,
				to: null,
				menu1: false,
				menu2: false
			};
		},
		methods: {
			submit() {
				let payload = {
					model: "coupons",
					data: {
						name: this.name,
						code: this.code,
						uses: this.uses,
						discount_id: this.discount_id,
						from: this.from,
						to: this.to
					}
				};
				this.create(payload).then(() => {
					this.clear();
					this.savingSuccessful = true;
					window.location.reload();
				});
			},
			clear() {
				this.name = null;
				this.code = null;
				this.uses = null;
				this.discount_id = null;
				this.from = new Date().toISOString().substr(0, 10);
				this.to = new Date().toISOString().substr(0, 10);
			},
			...mapActions({
				getAll: "getAll",
				getOne: "getOne",
				create: "create",
				delete: "delete"
			})
		}
	};
</script>
