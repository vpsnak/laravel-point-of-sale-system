<template>
    <div>
        <data-table
            icon="mdi-wallet-giftcard"
            title="Gift Cards"
            :headers="headers"
            data-url="gift-cards"
            btnTxt="New Gift Card"
            :newForm="form"
        >
            <template v-slot:item.enabled="{ item }">{{
                item.enabled ? "Yes" : "No"
            }}</template>

            <template v-slot:item.actions="{ item }">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            @click="rechargeGiftcardDialog(item)"
                            :loading="loading"
                            class="my-2"
                            icon
                            v-on="on"
                        >
                            <v-icon small>mdi-credit-card-plus</v-icon>
                        </v-btn>
                    </template>
                    <span>Recharge</span>
                </v-tooltip>

                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            small
                            :loading="loading"
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
                            :loading="loading"
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
    </div>
</template>

<script>
import { mapMutations, mapState } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
    mounted() {
        EventBus.$on(event => {
            this.setCheckoutDialog(true);
        });
    },
    data() {
        return {
            form: "giftCardForm",
            headers: [
                { text: "#", value: "id" },
                { text: "Name", value: "name" },
                { text: "Code", value: "code" },
                { text: "Enabled", value: "enabled" },
                { text: "Amount", value: "amount" },
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
        ...mapMutations("datatable", ["setLoading"]),
        ...mapMutations("dialog", ["setDialog", "viewItem", "editItem"]),
        ...mapMutations("cart", ["setCheckoutDialog"]),

        rechargeGiftcardDialog(item) {
            this.setDialog({
                width: 400,
                title: `Recharge the giftcard #${item.id}`,
                titleCloseBtn: true,
                icon: "mdi-wallet-giftcard",
                component: "RechargeGiftCardToCart",
                model: item,
                persistent: true
            });
        }
    }
};
</script>
