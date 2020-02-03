<template>
    <div>
        <interactiveDialog
            v-if="dialog.show"
            :show="dialog.show"
            :title="dialog.title"
            :titleCloseBtn="dialog.titleCloseBtn"
            :icon="dialog.icon"
            :width="dialog.width"
            :component="dialog.component"
            :content="dialog.content"
            :model="dialog.model"
            @action="dialogEvent"
            :persistent="dialog.persistent"
        ></interactiveDialog>
        <v-row>
            <v-col :cols="12">
                <h3 class="mb-2">Payment history</h3>
            </v-col>
        </v-row>
        <v-row>
            <v-col :cols="12">
                <v-data-table
                    :headers="headers"
                    :items="paymentHistory"
                    class="elevation-1"
                    disable-pagination
                    disable-filtering
                    hide-default-footer
                    :loading="loading || refundLoading || paymentHistoryLoading"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    @click="refundDialog(item)"
                                    icon
                                    v-on="on"
                                    :loading="
                                        loading ||
                                            refundLoading ||
                                            paymentHistoryLoading
                                    "
                                    v-if="
                                        item.status === 'approved' &&
                                            item.refunded !== true
                                    "
                                >
                                    <v-icon
                                        v-if="item.payment_type.type === 'cash'"
                                        >mdi-cash-refund</v-icon
                                    >
                                    <v-icon v-else
                                        >mdi-credit-card-refund</v-icon
                                    >
                                </v-btn>
                            </template>
                            <span>Refund</span>
                        </v-tooltip>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip
                            label
                            v-if="item.status === 'approved'"
                            color="success"
                        >
                            {{ item.status }}
                        </v-chip>
                        <v-chip
                            label
                            v-else-if="item.status === 'refunded'"
                            color="orange"
                        >
                            {{ item.status }}
                        </v-chip>
                        <v-chip v-else label color="red">{{
                            item.status
                        }}</v-chip>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        loading: Boolean
    },
    data() {
        return {
            paymentHistoryLoading: false,
            dialog: {
                show: false,
                width: 600,
                icon: "",
                title: "",
                titleCloseBtn: false,
                component: "",
                content: "",
                model: "",
                persistent: false
            },
            paymentID: null,
            headers: [
                {
                    text: "Payment ID",
                    value: "id"
                },
                {
                    text: "Operator",
                    value: "created_by.name"
                },
                {
                    text: "Date",
                    value: "created_at"
                },
                {
                    text: "Type",
                    value: "payment_type.name"
                },
                {
                    text: "Status",
                    value: "status"
                },
                {
                    text: "Amount (USD)",
                    value: "amount"
                },
                {
                    text: "Actions",
                    value: "actions"
                }
            ]
        };
    },
    computed: {
        paymentHistory: {
            get() {
                return this.$store.state.cart.payment_history;
            },
            set(value) {
                this.$store.commit("cart/setPaymentHistory", value);
            }
        },
        orderId() {
            return this.$store.state.cart.order.id;
        },
        refundLoading: {
            get() {
                return this.$store.state.cart.refundLoading;
            },
            set(value) {
                this.$store.state.cart.refundLoading = value;
            }
        }
    },
    mounted() {
        this.getPaymentHistory();
    },
    methods: {
        getPaymentHistory() {
            if (this.orderId) {
                this.paymentHistoryLoading = true;

                let payload = {
                    model: "payments",
                    keyword: this.orderId
                };
                this.search(payload)
                    .then(response => {
                        this.paymentHistory = response;
                    })
                    .finally(() => {
                        this.paymentHistoryLoading = false;
                    });
            }
        },
        dialogEvent(event) {
            if (event) {
                this.refund();
            }

            this.resetDialog();
        },
        refund() {
            this.refundLoading = true;
            let payload = {
                model: "payments",
                id: this.paymentID
            };

            this.delete(payload).then(response => {
                this.$emit("refund", response);
            });
        },

        refundDialog(item) {
            this.dialog = {
                show: true,
                width: 600,
                title: `Verify your password to refund payment #${item.id}`,
                titleCloseBtn: true,
                icon: "mdi-lock-alert",
                component: "passwordForm",
                model: { action: "verify" },
                persistent: true
            };

            this.paymentID = item.id;

            this.action = "cancelOrder";
        },

        resetDialog() {
            this.dialog = {
                show: false,
                width: 600,
                title: "",
                titleCloseBtn: false,
                icon: "",
                component: "",
                content: "",
                model: "",
                persistent: false
            };

            this.action = "";
        },

        ...mapActions(["search", "delete"]),

        beforeDestroy() {
            this.$off("refund");
        }
    }
};
</script>
