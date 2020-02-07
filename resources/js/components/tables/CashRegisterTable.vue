<template>
    <data-table
        icon="mdi-cash-register"
        title="Cash Registers"
        :headers="headers"
        data-url="cash-registers"
        btnTxt="New Cash Register"
        newForm="cashRegisterForm"
        :disableNewBtn="false"
    >
        <template v-slot:item.is_open="{ item }">{{
            item.is_open ? "Yes" : "No"
        }}</template>
        <template v-slot:item.actions="{ item }">
            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        small
                        :disabled="disableActions"
                        @click.stop="(item.form = form), editItem(item)"
                        class="my-2"
                        v-on="on"
                        icon
                    >
                        <v-icon small>edit</v-icon>
                    </v-btn>
                </template>
                <span>Edit</span>
            </v-tooltip>

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
            form: "cashRegisterForm",
            headers: [
                { text: "#", value: "id" },
                { text: "Name", value: "name" },
                { text: "Store", value: "store.name" },
                { text: "Barcode", value: "barcode" },
                { text: "Is open", value: "is_open" },
                { text: "Operator", value: "" },
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
        ...mapMutations("dialog", ["editItem", "viewItem"]),
        ...mapMutations("datatable", ["setLoading"])
    }
};
</script>
