<template>
    <v-container v-if="customerData">
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{customerData.first_name}} {{customerData.last_name}}</v-card-title>
                    <v-card-text>
                        <div class="subtitle-1">
                            Phone:
                            <a :href="'tel:' + customerData.phone">{{customerData.phone}}</a>
                        </div>
                        <div class="subtitle-1">
                            Email:
                            <a :href="'mailto:' + customerData.email">{{customerData.email}}</a>
                        </div>
                        <div class="subtitle-1">Company name: {{customerData.company_name}}</div>
                        <div class="subtitle-1">Created at: {{customerData.created_at}}</div>
                        <div class="subtitle-1">Updated at: {{customerData.updated_at}}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" v-if="customerData.addresses">
                <v-card>
                    <v-card-title>Addreses</v-card-title>
                    <v-card-text>
                        <v-simple-table dense>
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <th class="text-left">First name</th>
                                        <th class="text-left">Last name</th>
                                        <th class="text-left">Phone</th>
                                        <th class="text-left">Post code</th>
                                        <th class="text-left">Country id</th>
                                        <th class="text-left">Region</th>
                                        <th class="text-left">Area code</th>
                                        <th class="text-left">City</th>
                                        <th class="text-left">Street</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="address in customerData.addresses" :key="address.id">
                                        <td>{{ address.first_name }}</td>
                                        <td>{{ address.last_name }}</td>
                                        <td>
                                            <a :href="'tel:' + address.phone">{{ address.phone }}</a>
                                        </td>
                                        <td>{{ address.postcode }}</td>
                                        <td>{{ address.country_id }}</td>
                                        <td>{{ address.region }}</td>
                                        <td>{{ address.area_code_id }}</td>
                                        <td>{{ address.city }}</td>
                                        <td>{{ address.street }}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
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
            customer: null
        };
    },
    mounted() {
        if (this.model)
            this.getOne({
                model: "customers",
                data: {
                    id: this.model.id
                }
            }).then(result => {
                this.customer = result;
            });
    },
    computed: {
        customerData() {
            return this.customer;
        }
    },
    methods: {
        ...mapActions({
            getOne: "getOne"
        })
    }
};
</script>
