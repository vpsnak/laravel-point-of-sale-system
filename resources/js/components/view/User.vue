<template>
    <v-container v-if="userData">
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{userData.name}}</v-card-title>
                    <v-card-text>
                        <div class="subtitle-1">
                            Email:
                            <a :href="'mailto:' + userData.email">{{userData.email}}</a>
                        </div>
                        <div class="subtitle-1">Email verified at: {{userData.email_verified_at}}</div>
                        <div class="subtitle-1">Created at: {{userData.created_at}}</div>
                        <div class="subtitle-1">Updated at: {{userData.updated_at}}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" v-if="userData.open_registers.length > 0">
                <v-card>
                    <v-card-title>Open Registers</v-card-title>
                    <v-card-text>
                        <v-simple-table dense>
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <th class="text-left">Cash Register id</th>
                                        <th class="text-left">Opening amount</th>
                                        <th class="text-left">Closing amount</th>
                                        <th class="text-left">Status</th>
                                        <th class="text-left">Opening time</th>
                                        <th class="text-left">Closing time</th>
                                        <th class="text-left">Opened by</th>
                                        <th class="text-left">Closed by</th>
                                        <th class="text-left">note</th>
                                        <th class="text-left">Created at</th>
                                        <th class="text-left">Updated at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="open_register in userData.open_registers"
                                        :key="open_register.id"
                                    >
                                        <td>{{ open_register.cash_register_id }}</td>
                                        <td>{{ open_register.opening_amount }}</td>
                                        <td>{{ open_register.closing_amount }}</td>
                                        <td>{{ open_register.status }}</td>
                                        <td>{{ open_register.opening_time }}</td>
                                        <td>{{ open_register.closing_time }}</td>
                                        <td>{{ open_register.opened_by }}</td>
                                        <td>{{ open_register.closed_by }}</td>
                                        <td>{{ open_register.note }}</td>
                                        <td>{{ open_register.created_at }}</td>
                                        <td>{{ open_register.updated_at }}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="8" v-else>
                <v-card-title>Open registers</v-card-title>
                <v-card-text>There are no open register for this user</v-card-text>
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
            user: null
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
