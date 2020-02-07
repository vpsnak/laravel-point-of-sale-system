<template>
    <data-table
        icon="mdi-domain"
        :headers="headers"
        data-url="companies"
        title="Companies"
        btnTxt="New company"
        :newForm="form"
        :disableNewBtn="false"
    >
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
            form: "companyForm",
            headers: [
                { text: "#", value: "id" },
                { text: "Name", value: "name" },
                { text: "Tax number", value: "tax_number" },
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
