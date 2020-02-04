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
                        <template
                            v-slot:item.customer="{ item }"
                        >{{ item.customer ? item.customer.email : "Guest" }}</template>
                        <template v-slot:item.total="{ item }">$ {{ item.total.toFixed(2) }}</template>
                        <template
                            v-slot:item.total_paid="{ item }"
                        >$ {{ item.total_paid.toFixed(2) }}</template>

                        <template v-slot:item.status="{ item }">
                            <span :class="statusColor(item.status)">
                                <b>{{ parseStatusName(item.status) }}</b>
                            </span>
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
                    text: "#",
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
                    text: "Total",
                    value: "total"
                },
                {
                    text: "Total paid",
                    value: "total_paid"
                },
                {
                    text: "Created by",
                    value: "created_by.name"
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
    },
    methods: {
        parseStatusName(status) {
            return _.upperFirst(status.replace("_", " "));
        },
        statusColor(status) {
            switch (status) {
                case "canceled":
                    return "red--text";
                case "pending":
                    return "primary--text";
                case "pending_payment":
                    return "primary--text";
                case "paid":
                    return "cyan--text";
                case "complete":
                    return "green--text";
                default:
                    return "";
            }
        }
    }
};
</script>
