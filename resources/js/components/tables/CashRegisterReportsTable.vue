<template>
    <data-table v-if="data_table.model">
        <template v-slot:item.actions="{ item }">
            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        small
                        :disabled="data_table.loading"
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
    mounted() {
        this.setDataTable({
            icon: "mdi-file-document-box",
            title: "Cash Register Reports",
            model: "cash-register-reports",
            component: this.form,
            disableNewBtn: true,
            loading: true
        });
    },
    data() {
        return {
            form: "cashRegisterReports"
        };
    },

    computed: {
        ...mapState("datatable", ["data_table"])
    },
    methods: {
        ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
        ...mapMutations("datatable", [
            "setLoading",
            "setDataTable",
            "resetDataTable"
        ]),
        ...mapMutations("cart", ["setCheckoutDialog"])
    }
};
</script>
