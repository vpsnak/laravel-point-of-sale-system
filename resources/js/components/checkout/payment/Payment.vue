<template>
    <div>
        <paymentHistory @refund="refund"></paymentHistory>

        <paymentActions
            :remaining="remaining"
            :loading="paymentActionsLoading"
            @sendPayment="sendPayment"
        ></paymentActions>
    </div>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
    data() {
        return {
            order_remaining: null,
            paymentTypes: [],

            paymentHistoryLoading: false,
            paymentActionsLoading: false,

            payment: {
                type: null,
                amount: null
            }
        };
    },

    mounted() {
        // if (this.order_id) {
        //     this.remaining = (this.order.total - this.order.total_paid).toFixed(
        //         2
        //     );
        //     let payload = {};
        //     if (this.remaining < 0) {
        //         payload = {
        //             order_status: this.order.status,
        //             change: Math.abs(this.remaining)
        //         };
        //     } else {
        //         payload = {
        //             order_status: this.order.status,
        //             change: false
        //         };
        //     }
        //     this.$emit("payment", payload);
        // }
    },

    computed: {
        ...mapState("cart", ["order_id", "order_status", "payments"]),

        remaining: {
            get() {
                return this.order_remaining;
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
        refundLoading: {
            get() {
                return this.$store.state.cart.refundLoading;
            },
            set(value) {
                this.$store.state.cart.refundLoading = value;
            }
        }
    },

    methods: {
        ...mapMutations("cart", ["setPaymentHistory"]),
        ...mapMutations(["setNotification"]),
        ...mapActions(["search", "create", "getAll", "getOne"]),

        sendPayment(event) {
            this.paymentActionsLoading = true;

            let payload = {
                model: "payments",
                data: {
                    payment_type: event.paymentType,
                    amount: event.paymentAmount || null,
                    order_id: this.order_id
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
                    break;
            }

            this.create(payload)
                .then(response => {
                    this.setOrderChange(state, response.change);
                    this.setOrderRemaining(state, response.remaining);
                    this.setOrderStatus(state, response.order_status);

                    this.setPaymentHistory(response.payment);

                    if (payload.data.payment_type === "house-account") {
                        this.$store.state.cart.order.customer.house_account_limit -=
                            payload.data.amount;
                    }

                    let notification = {
                        msg: "Payment received",
                        type: "success"
                    };
                    this.setNotification(notification);

                    this.$emit("payment", response);
                })
                .catch(error => {
                    if (_.has(error.response.data, "payment")) {
                        this.setPaymentHistory(error.response.data.payment);
                    }
                })
                .finally(() => {
                    this.paymentAmount = null;
                    this.paymentActionsLoading = false;
                    this.$store.state.cart.paymentLoading = false;
                });
        },
        refund(event) {
            if (event.refunded_payment_id) {
                const index = _.findIndex(this.payments, [
                    "id",
                    event.refunded_payment_id
                ]);

                // @TODO set payment to refunded
                // this.payments[index].refunded = true;

                this.setPaymentHistory(event.refund);
            }

            this.setOrderChange(state, event.change);
            this.setOrderRemaining(state, event.remaining);
            this.setOrderStatus(state, event.order_status);

            const notification = {
                msg: event.msg,
                type: event.status
            };

            this.$emit("payment", event);

            this.$store.state.cart.refundLoading = false;
            this.setNotification(notification);
        }
    },

    beforeDestroy() {
        this.$off("payment");
    }
};
</script>
