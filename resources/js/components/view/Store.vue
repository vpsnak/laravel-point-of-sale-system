<template>
    <v-container v-if="storeData">
        <v-row>
            <v-col cols="12" md="4">
                <v-card>
                    <v-card-title>{{storeData.name}}</v-card-title>
                    <v-card-text>
                        <div class="subtitle-1">Taxable: {{storeData.taxable ? 'Yes' : 'No'}}</div>
                        <div class="subtitle-1">Is default: {{storeData.is_default ? 'Yes' : 'No'}}</div>
                        <div class="subtitle-1">Tax id: {{storeData.tax_id}}</div>
                        <div class="subtitle-1">Created by: {{storeData.created_by}}</div>
                        <div class="subtitle-1">Created at: {{storeData.created_at}}</div>
                        <div class="subtitle-1">Updated at: {{storeData.updated_at}}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="8" v-if="storeData.cash_registers">
                <v-card>
                    <v-card-title>Cash Registers</v-card-title>
                    <v-card-text>
                        <v-simple-table dense>
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <th class="text-left">Name</th>
                                        <th class="text-left">Created by</th>
                                        <th class="text-left">Created at</th>
                                        <th class="text-left">Updated at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="cash_register in storeData.cash_registers"
                                        :key="cash_register.id"
                                    >
                                        <td>{{ cash_register.name }}</td>
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
            store: null
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
