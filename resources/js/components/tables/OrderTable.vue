<template>
    <div>
        <v-container>
            <v-row>
                <v-col cols="12">
                    <prop-data-table
                        :tableHeaders="headers"
                        data-url="orders"
                        tableTitle="Orders"
                        tableBtnTitle="New Order"
                        :tableBtnDisable="true"
                        tableViewComponent="order"
                    >
                        <template v-slot:item.customer="{ item }">{{
                            item.customer ? item.customer.email : "Guest"
                        }}</template>
                        <template v-slot:item.created_by="{ item }">{{
                            item.created_by.name
                        }}</template>
                        <template v-slot:item.status="{ item }">
                            <v-chip
                                label
                                v-if="item.status === 'approved'"
                                color="success"
                                >{{ item.status }}</v-chip
                            >
                            <v-chip
                                label
                                v-else-if="item.status === 'refunded'"
                                color="orange"
                                >{{ item.status }}</v-chip
                            >
                            <v-chip
                                label
                                v-else-if="item.status === 'pending'"
                                color="teal darken-4"
                                >{{ item.status }}</v-chip
                            >
                            <v-chip
                                label
                                v-else-if="item.status === 'pending_payment'"
                                color="cyan darken-4"
                                >{{ item.status }}</v-chip
                            >
                            <v-chip v-else label color="red">
                                {{ item.status }}
                            </v-chip>
                        </template>
                    </prop-data-table>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapMutations } from "vuex";

export default {
    data() {
        return {
            headers: [
                {
                    text: "ID",
                    value: "id"
                },
                {
                    text: "Customer",
                    value: "customer"
                },
                {
                    text: "Store",
                    value: "store.name"
                },
                {
                    text: "Status",
                    value: "status"
                },
                {
                    text: "Created by",
                    value: "created_by"
                },
                {
                    text: "tax",
                    value: "tax"
                },
                {
                    text: "Subtotal",
                    value: "subtotal"
                },
                {
                    text: "Created at",
                    value: "created_at"
                },
                {
                    text: "Actions",
                    value: "action"
                }
            ]
        };
    }
};
</script>
