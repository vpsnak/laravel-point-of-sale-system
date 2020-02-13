<template>
    <v-card outlined>
        <v-card-title>
            <v-icon left>mdi-currency-usd</v-icon>
            <span class="subheading">
                Payment History
            </span>
        </v-card-title>

        <v-data-table
            no-data-text="No payments have been made"
            dense
            height="15vh"
            :headers="headers"
            :items="payments"
            class="elevation-1"
            disable-pagination
            disable-filtering
            hide-default-footer
            :loading="loading || refundLoading || paymentHistoryLoading"
        >
            <template v-slot:item.status="{ item }">
                <span :class="statusColor(item.status)">
                    <b>{{ parseStatus(item.status) }}</b>
                </span>
            </template>

            <template v-slot:item.actions="{ item }" v-if="!$props.editOrder">
                <v-tooltip
                    bottom
                    v-if="item.status === 'approved' && !item.refunded"
                >
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
                        >
                            <v-icon v-if="item.payment_type.type === 'cash'">
                                mdi-cash-refund
                            </v-icon>
                            <v-icon v-else>mdi-credit-card-refund</v-icon>
                        </v-btn>
                    </template>
                    <span>Rollback transaction</span>
                </v-tooltip>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import { mapActions, mapState, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
    props: {
        editOrder: Boolean,
        loading: Boolean
    },

    mounted() {
        if (!this.$props.editOrder) {
            this.headers.push({
                text: "Actions",
                value: "actions"
            });
        }
        EventBus.$on("payment-history-refund", event => {
            console.info(event);
            if (event.payload && this.selected_payment) {
                this.refund();
            }
        });
    },

    beforeDestroy() {
        this.$off("refund");
        EventBus.$off();
    },

    data() {
        return {
            paymentHistoryLoading: false,
            selected_payment: null,
            headers: [
                {
                    text: "#",
                    value: "id",
                    sortable: false
                },
                {
                    text: "Operator",
                    value: "created_by.name",
                    sortable: false
                },
                {
                    text: "Date",
                    value: "created_at",
                    sortable: false
                },
                {
                    text: "Type",
                    value: "payment_type.name",
                    sortable: false
                },
                {
                    text: "Status",
                    value: "status",
                    sortable: false
                },
                {
                    text: "Amount (USD)",
                    value: "amount",
                    sortable: false
                }
            ]
        };
    },
    computed: {
        ...mapState("cart", ["order", "refund_loading", "payments"]),

        refundLoading: {
            get() {
                return this.refund_loading;
            },
            set(value) {
                this.setRefundLoading(value);
            }
        }
    },
    methods: {
        ...mapMutations("dialog", ["setDialog"]),
        ...mapActions(["search", "delete"]),
        ...mapActions("cart", ["setPaymentHistory", "setRefundLoading"]),

        parseStatus(status) {
            return _.upperFirst(status);
        },
        statusColor(status) {
            switch (status) {
                case "approved":
                    return "green--text";
                case "failed":
                    return "red--text";
                case "refunded":
                    return "orange--text";
                default:
                    return null;
            }
        },
        refund() {
            this.refundLoading = true;
            let payload = {
                model: "payments",
                id: this.selected_payment.id
            };

            this.delete(payload)
                .then(response => {
                    this.$emit("refund", response);
                })
                .catch(error => {
                    if (error.response.data.refund) {
                        this.payments = error.response.data.refund;
                    }

                    this.$emit("refund", error.response.data);
                });
        },
        refundDialog(item) {
            this.selected_payment = item;

            this.setDialog({
                show: true,
                width: 600,
                title: `Verify your password to rollback payment #${item.id}`,
                titleCloseBtn: true,
                icon: "mdi-lock-alert",
                component: "passwordForm",
                model: { action: "verify" },
                persistent: true,
                eventChannel: "payment-history-refund"
            });
        }
    }
};
</script>
