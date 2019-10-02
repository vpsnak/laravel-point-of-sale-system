<template>
    <v-dialog v-model="show" persistent max-width="600px" transition="dialog-bottom-transition">
        <v-card>
            <v-card-title>
                <div class="text-center">
                    <v-row align="center" justify="center">
                        <v-col align="center" justify="center">
                            <h5
                                class="text-center"
                            >Select store and cash register NOW OR YOU DONT LEAVE</h5>
                        </v-col>
                    </v-row>
                </div>
            </v-card-title>
            <v-card-text>
                <v-select
                    v-model="store_id"
                    :items="stores"
                    :rules="[v => !!v || 'Store is required']"
                    label="Stores"
                    required
                    item-text="name"
                    item-value="id"
                ></v-select>
                <v-select
                    v-model="cash_register_id"
                    :items="cash_registers"
                    :rules="[v => !!v || 'Cash Register is required']"
                    label="Cash Register"
                    required
                    item-text="name"
                    item-value="id"
                ></v-select>
                <v-divider></v-divider>
            </v-card-text>
            <v-card-actions>
                <div class="flex-grow-1"></div>
                <v-btn color="primary" text @click="close">Close</v-btn>
            </v-card-actions>
            <v-alert
                :value="alert"
                color="pink"
                dark
                border="top"
                icon="fas fa-grin-squint"
                transition="scale-transition"
            >I said select them !!</v-alert>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapActions } from "vuex";

export default {
    data: () => ({
        show: true,
        cash_register_id: null,
        store_id: null,
        stores: [],
        cash_registers: [],
        alert: false
    }),
    mounted() {
        this.getAll({
            model: "stores"
        }).then(stores => {
            this.stores = stores;
        });
        this.getAll({
            model: "cash-registers"
        }).then(cash_registers => {
            this.cash_registers = cash_registers;
        });
    },
    computed: {},
    methods: {
        close() {
            if (this.store_id != null && this.cash_register_id != null) {
                this.show = false;
            } else {
                this.alert = true;
            }
        },
        getAllStores() {
            this.getAll({
                model: "stores"
            }).then(stores => {
                this.stores = stores;
            });
        },
        getAllCashRegisters() {
            this.getAll({
                model: "cash-registers"
            }).then(cash_registers => {
                this.cash_registers = cash_registers;
            });
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