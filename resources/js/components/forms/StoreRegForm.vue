<template>
    <v-card>
        <v-card-title>
            <div class="text-center">
                <v-row align="center" justify="center">
                    <v-col align="center" justify="center">
                        <h6 class="text-center">Select store and cash register</h6>
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
            <v-btn class="mr-4" @click="submit">submit</v-btn>
            <v-btn @click="clear">clear</v-btn>
        </v-card-text>
    </v-card>
</template>
<script>
import { mapActions } from "vuex";

export default {
    data: () => ({
        cash_register_id: null,
        store_id: null,
        stores: [],
        cash_registers: []
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
    methods: {
        submit() {},
        clear() {
            this.cash_register_id = null;
            this.store_id = null;
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
