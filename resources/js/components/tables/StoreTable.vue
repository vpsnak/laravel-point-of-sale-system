<template>
    <data-table
        title="Stores"
        icon="mdi-store"
        :headers="headers"
        data-url="stores"
        btnTxt="New Store"
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
            form: "storeForm",
            headers: [
                { text: "Id", value: "id" },
                { text: "Name", value: "name" },
                { text: "Phone", value: "phone" },
                { text: "Street", value: "street" },
                { text: "Postcode", value: "postcode" },
                { text: "City", value: "city" },
                { text: "Tax", value: "tax.name" },
                { text: "Company", value: "company.name" },
                { text: "Created by", value: "created_by" },
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
