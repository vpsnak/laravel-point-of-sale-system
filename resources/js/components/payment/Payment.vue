<template>
    <div>
        <paymentHistory
            :paymentHistory="paymentHistory"
            :loading="paymentHistoryLoading"
            v-if="history"
            @refund="refund"
        ></paymentHistory>

        <v-divider></v-divider>

        <paymentActions
            :order_id="order_id"
            :types="paymentTypes"
            :remaining="remaining"
            :loading="paymentActionsLoading"
            title="Order payment"
            paymentBtnTxt="Send payment"
            v-if="actions && (remaining > 0 || remaining === undefined)"
            @sendPayment="sendPayment"
        ></paymentActions>
    </div>
</template>

<script>
import { mapActions, mapMutations } from "vuex";

export default {
    props: {
        order_id: Number,
        history: Boolean,
        actions: Boolean
    },
    data() {
        return {
            show_actions: undefined,
            order: {},
            order_remaining: undefined,
            payment_history: [],
            orderHistory: [],
            paymentTypes: [],

            paymentHistoryLoading: false,
            paymentActionsLoading: false,

            payment: {
                type: null,
                amount: null
            }
        };
    },

    computed: {
        remaining: {
            get() {
                if (parseFloat(this.order_remaining) >= 0) {
                    return this.order_remaining;
                } else if (parseFloat(this.order_remaining) < 0) {
                    return this.order_remaining;
                } else {
                    return undefined;
                }
            },
            set(value) {
                this.order_remaining = value;
            }
        },
        paymentType: {
            get() {
                return this.payment.type;
            },
            set(value) {
                this.payment.type = value;
            }
        },
        paymentAmount: {
            get() {
                return this.payment.amount;
            },
            set(value) {
                this.payment.amount = value;
            }
        },
        paymentHistory: {
            get() {
                return this.payment_history;
            },
            set(value) {
                this.payment_history = value;
            }
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
        this.init();
    },

    methods: {
        init() {
            this.getOrder();

            if (this.$props.history) {
                this.getPaymentHistory();
            }

            if (this.$props.actions) {
                this.getPaymentTypes();
            }
        },
        getOrder() {
            if (this.$props.order_id) {
                let payload = {
                    model: "orders",
                    data: { id: this.$props.order_id }
                };
                this.getOne(payload)
                    .then(response => {
                        this.order = response;

                        this.$store.state.cart.order = response;

                        this.remaining =
                            this.order.total - this.order.total_paid;
                        this.$emit("amountPending", this.remaining);
                    })
                    .finally(() => {
                        this.refundLoading = false;
                    });
            }
        },
        getPaymentHistory() {
            if (this.$props.order_id > 0) {
                this.paymentHistoryLoading = true;

                let payload = {
                    model: "payments",
                    keyword: this.$props.order_id
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

        getPaymentTypes() {
            this.getAll({ model: "payment-types" }).then(response => {
                this.paymentTypes = response;
            });
        },

        sendPayment(event) {
            this.paymentActionsLoading = true;

            let payload = {
                model: "payments",
                data: {
                    payment_type: event.paymentType,
                    amount: event.paymentAmount || null,
                    order_id: this.$props.order_id,
                    cash_register_id: this.$store.state.cashRegister.id
                }
            };

            switch (event.paymentType) {
                default:
                case "cash":
                    break;
                case "card":
                    payload.data["card"] = event.card;
                    break;
                case "coupon":
                case "giftcard":
                    payload.data["code"] = event.code;
                    break;
                case "house-account":
                    payload.data["house_account_number"] =
                        event.house_account_number;
            }

            this.create(payload)
                .then(response => {
                    this.$store.state.cart.order.remaining = this.order_remaining =
                        response.total - response.total_paid;

                    let notification = {
                        msg: "Payment received",
                        type: "success"
                    };
                    this.setNotification(notification);

                    if (payload.data.payment_type === "house-account") {
                        this.$store.state.cart.customer.house_account_limit -=
                            payload.data.amount;
                    }
                })
                .finally(() => {
                    this.paymentAmount = null;
                    this.paymentActionsLoading = false;

                    this.init();
                });
        },
        refund(event) {
            let notification = {
                msg: event.msg,
                type: event.status
            };
            this.setNotification(notification);

            this.init();
        },

        ...mapActions(["search", "create", "getAll", "getOne"]),
        ...mapMutations(["setNotification"])
    },
    beforeDestroy() {
        this.$off("amountPending");
    }
};
</script>
