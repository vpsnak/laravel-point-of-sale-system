<template>
    <div>
        <paymentHistory :payments="payments" :loading="paymentHistoryLoading" @refund="refund"></paymentHistory>

        <v-divider></v-divider>

        <paymentActions
            :remaining="remaining"
            :loading="paymentActionsLoading"
            v-if="remaining >= 0"
            @sendPayment="sendPayment"
        ></paymentActions>
    </div>
</template>

<script>
import { mapActions, mapMutations } from "vuex";

export default {
    data() {
        return {
            payments: [],
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

    mounted() {
        if (this.orderId) {
            if (this.$store.state.cart.order.change) {
                this.remaining =
                    parseFloat(this.$store.state.cart.order.change) * -1;
            } else {
                this.remaining =
                    parseFloat(this.$store.state.cart.order.total) -
                    parseFloat(this.$store.state.cart.order.total_paid);
            }

            this.$emit("payment", this.remaining);
        }
    },

    computed: {
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
                    this.payments.push(response.payment);

                    this.remaining =
                        parseFloat(response.payment.order.total) -
                        parseFloat(response.payment.order.total_paid);

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
                .catch(error => {
                    if (error.response.data.payment) {
                        this.payments.push(error.response.data.payment);
                    }
                })
                .finally(() => {
                    this.paymentAmount = null;
                    this.paymentActionsLoading = false;
                    this.$store.state.cart.paymentLoading = false;

                    this.$emit("payment", this.remaining);
                });
        },
        refund(event) {
            const index = _.findIndex(this.payments, [
                "id",
                event.refunded_payment_id
            ]);
            this.payments[index].refunded = true;
            this.payments.push(event.refund);

            this.remaining =
                parseFloat(event.refund.order.total) -
                parseFloat(event.refund.order.total_paid);

            const notification = {
                msg: event.msg,
                type: event.status
            };

            this.$emit("payment", this.remaining);

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
