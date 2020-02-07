<template>
    <data-table
        icon="mdi-file-document-box"
        title="Cash Register Reports"
        :headers="headers"
        data-url="cash-register-reports"
        tableBtnTitle="New Cash Register"
        :tableBtnDisable="true"
    >
        <template v-slot:item.actions="{ item }">
            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        small
                        :disabled="disableActions"
                        @click.stop="(item.form = form), viewItem(item)"
                        class="my-2"
                        v-on="on"
                        icon
                    >
                        <v-icon small>mdi-eye</v-icon>
                    </v-btn>
                </template>
                <span>View</span>
            </v-tooltip>
        </template>
    </data-table>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
    data() {
        return {
            form: "cashRegisterReports",
            headers: [
                { text: "#", value: "id" },
                { text: "Report name", value: "report_name" },
                { text: "Report type", value: "report_type" },
                { text: "Cash register ID", value: "cash_register_id" },
                { text: "Opening amount", value: "opening_amount" },
                { text: "Closing amount", value: "closing_amount" },
                { text: "Subtotal", value: "subtotal" },
                { text: "Tax", value: "tax" },
                { text: "Total", value: "total" },
                { text: "Actions", value: "actions" }
            ]
        };
    },

    computed: {
        ...mapState("datatable", ["loading"]),

        disableActions: {
            get() {
                return this.loading;
            },
            set(value) {
                this.setLoading(value);
            }
        }
    },
    methods: {
        ...mapMutations("dialog", ["viewItem", "editItem"]),
        ...mapMutations("datatable", ["setLoading"])
    }
};
</script>
