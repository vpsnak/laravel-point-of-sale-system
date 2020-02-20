<template>
  <v-container>
    <v-row v-if="storeData">
      <v-col cols="12">
        <v-card>
          <v-card-title>{{ storeData.name }}</v-card-title>
          <v-card-text>
            <div class="subtitle-1">Tax: {{ storeData.tax.name }}</div>
            <div class="subtitle-1">Created by: {{ user.name }}</div>
            <div class="subtitle-1">Phone: {{ storeData.phone }}</div>
            <div class="subtitle-1">Street: {{ storeData.street }}</div>
            <div class="subtitle-1">Postcode: {{ storeData.postcode }}</div>
            <div class="subtitle-1">City: {{ storeData.city }}</div>
            <div class="subtitle-1">Created at: {{ storeData.created_at }}</div>
            <div class="subtitle-1">Updated at: {{ storeData.updated_at }}</div>
          </v-card-text>
        </v-card>
      </v-col>
      <!-- <v-col cols="12" md="8" v-if="storeData.cash_registers">
				<v-card>
					<v-card-title>Cash Registers</v-card-title>
					<v-card-text>
						<v-simple-table dense>
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">Name</th>
										<th class="text-left">Barcode</th>
										<th class="text-left">Pos terminal</th>
										<th class="text-left">Printer</th>
										<th class="text-left">Created by</th>
										<th class="text-left">Created at</th>
										<th class="text-left">Updated at</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="cash_register in storeData.cash_registers" :key="cash_register.id">
										<td>{{ cash_register.name }}</td>
										<td>{{ cash_register.barcode }}</td>
										<td>{{ cash_register.pos_terminal }}</td>
										<td>{{ cash_register.printer }}</td>
										<td>{{ cash_register.created_by }}</td>
										<td>{{ cash_register.created_at }}</td>
										<td>{{ cash_register.updated_at }}</td>
									</tr>
								</tbody>
							</template>
						</v-simple-table>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" md="8" v-else>
				<v-card-title>Cash Registers</v-card-title>
				<v-card-text>There are no cash register assigned to this store</v-card-text>
			</v-col>-->
    </v-row>
    <v-row v-else>
      <v-col cols="12" align="center" justify="center">
        <v-progress-circular
          indeterminate
          color="secondary"
        ></v-progress-circular>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Number
  },
  data() {
    return {
      store: null,
      user: ""
    };
  },
  mounted() {
    if (this.model)
      this.getOne({
        model: "stores",
        data: {
          id: this.model.id
        }
      }).then(result => {
        this.store = result;
        this.getOne({
          model: "users",
          data: {
            id: this.storeData.created_by
          }
        }).then(response => {
          this.user = response;
        });
      });
  },
  computed: {
    storeData() {
      return this.store;
    }
  },
  methods: {
    ...mapActions({
      getOne: "getOne"
    })
  }
};
</script>
