<template>
    <data-table
        icon="mdi-account-card-details"
        title="Addresses"
        :headers="headers"
        data-url="addresses"
        btnTxt="New Address"
        :newForm="form"
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
            form: "addressForm",
            headers: [
                { text: "#", value: "id" },
                { text: "First Name", value: "first_name" },
                { text: "Last Name", value: "last_name" },
                { text: "Street", value: "street" },
                { text: "Street 2", value: "street2" },
                { text: "City", value: "city" },
                { text: "Country ID", value: "country_id" },
                { text: "Region", value: "address_region.default_name" },
                { text: "Postcode", value: "postcode" },
                { text: "Phone", value: "phone" },
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
