<template>
    <v-row align="center" no-gutters>
        <v-col :cols="6">
            <v-card outlined>
                <v-card-title>
                    <v-icon left>mdi-package-variant</v-icon>
                    <span class="subheading">
                        Items
                    </span>
                </v-card-title>
            </v-card>
        </v-col>
        <v-col :cols="6">
            <v-card outlined>
                <v-card-title>
                    <v-icon left>mdi-account-outline</v-icon>
                    <span class="subheading">
                        Customer
                    </span>
                </v-card-title>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>
import { mapState } from "vuex";

export default {
    computed: {
        ...mapState("cart", ["order"])
    },
    methods: {
        parseStatusName(value) {
            return _.upperFirst(value.replace("_", " "));
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
