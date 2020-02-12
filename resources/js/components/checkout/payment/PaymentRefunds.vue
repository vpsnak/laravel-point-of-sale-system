<template>
    <v-card outlined>
        <v-card-title>
            <v-icon left>mdi-cursor-default-click-outline</v-icon>
            <span class="subheading">
                Actions
            </span>
        </v-card-title>

        <v-container>
            <v-row justify="center" align="center" dense>
                <v-col :cols="6">
                    <v-card outlined class="pa-2">
                        <v-text-field
                            v-model="amount"
                            label="Make a refund"
                            prefix="$"
                            prepend-icon="mdi-credit-card-refund-outline"
                            append-outer-icon="mdi-send-outline"
                            @click:append-outer="submitRefund()"
                        ></v-text-field>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-card>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
    data() {
        return {
            amount: null
        };
    },

    computed: {
        ...mapState("cart", ["order"])
    },

    methods: {
        submitRefund() {}
    }
};
</script>
'pending', pending_payment
