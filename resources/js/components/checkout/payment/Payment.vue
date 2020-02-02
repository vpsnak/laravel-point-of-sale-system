<template>
    <div>
        <paymentHistory
            :loading="paymentHistoryLoading"
            @refund="refund"
        ></paymentHistory>

        <v-divider></v-divider>

        <paymentActions
            :remaining="remaining"
            :loading="paymentActionsLoading"
            @sendPayment="sendPayment"
        ></paymentActions>
    </div>
</template>

<script>
import { mapActions, mapMutations } from "vuex";

export default {
    data() {
        return {
            order_status: null,
            order_remaining: null,
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
        payments: {
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
        sendPayment(event) {
            this.paymentActionsLoading = true;

            let payload = {
                model: "payments",
                data: {
                    payment_type: event.paymentType,
                    amount: event.paymentAmount || null,
                    order_id: this.orderId
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
                    this.payments = response.payment;
                    this.remaining = response.remaining;
                    this.order_status = response.order_status;

                    let notification = {
                        msg: "Payment received",
                        type: "success"
                    };
                    this.setNotification(notification);

                    if (payload.data.payment_type === "house-account") {
                        this.$store.state.cart.customer.house_account_limit -=
                            payload.data.amount;
                    }

                    this.$emit("payment", response);
                })
                .catch(error => {
                    if (error.response.data.payment) {
                        this.payments = error.response.data.payment;
                    }
                })
                .finally(() => {
                    this.paymentAmount = null;
                    this.paymentActionsLoading = false;
                    this.$store.state.cart.paymentLoading = false;
                });
        },
        refund(event) {
            const index = _.findIndex(this.payments, [
                "id",
                event.refunded_payment_id
            ]);
            this.payments[index].refunded = true;
            this.payments.push(event.refund);

            this.remaining = event.remaining;

            const notification = {
                msg: event.msg,
                type: event.status
            };

            this.$emit("payment", event);

            this.$store.state.cart.refundLoading = false;
            this.setNotification(notification);
        },

        ...mapActions(["search", "create", "getAll", "getOne"]),
        ...mapMutations(["setNotification"])
    },

    beforeDestroy() {
        this.$off("payment");
    }
};
</script>
