<template>
    <div>
        <v-row justify="center" align="center" class="flex-column my-3">
            <h3 class="py-3">{{ title }}</h3>
            <v-btn-toggle v-model="paymentType" mandatory @change="clearState">
                <v-btn
                    v-for="(paymentType, index) in paymentTypes"
                    :key="index"
                    :value="paymentType.type"
                    :disabled="loading || orderLoading"
                >
                    <v-icon class="pr-2">{{ paymentType.icon }}</v-icon>
                    {{ paymentType.name }}
                </v-btn>
            </v-btn-toggle>
        </v-row>
        <v-row
            justify="center"
            align="center"
            v-if="paymentType === 'card'"
            class="my-3"
        >
            <div class="pr-2">
                <v-text-field
                    autocomplete="off"
                    label="Card number"
                    type="number"
                    prepend-inner-icon="mdi-credit-card"
                    v-model="card.number"
                    style="max-width:300px;"
                    :disabled="loading || orderLoading"
                ></v-text-field>
                <v-text-field
                    autocomplete="off"
                    label="CVC/CVV"
                    type="number"
                    prepend-inner-icon="mdi-lock"
                    v-model="card.cvc"
                    style="max-width:300px;"
                    :disabled="loading || orderLoading"
                ></v-text-field>
            </div>
            <div class="pl-2">
                <v-text-field
                    autocomplete="off"
                    label="Card holder's name"
                    prepend-inner-icon="mdi-account-box"
                    v-model="card.card_holder"
                    style="max-width:300px;"
                    :disabled="loading || orderLoading"
                ></v-text-field>
                <v-text-field
                    autocomplete="off"
                    :disabled="loading || orderLoading"
                    label="Exp date"
                    v-model="card.exp_date"
                    style="max-width:300px;"
                    prepend-inner-icon="mdi-calendar"
                ></v-text-field>
            </div>
        </v-row>
        <v-row
            justify="center"
            align="center"
            class="flex-column my-3"
            v-else-if="paymentType === 'coupon' || paymentType === 'giftcard'"
        >
            <v-text-field
                label="Code"
                :prepend-inner-icon="getIcon()"
                :disabled="loading || orderLoading"
                v-model="code"
                style="max-width:300px;"
            ></v-text-field>
        </v-row>
        <v-row
            justify="center"
            align="center"
            class="my-3"
            v-if="paymentType !== 'coupon'"
        >
            <v-col :lg="5" class="d-flex justify-space-between align-center">
                <span class="title" v-if="remainingAmount !== undefined">
                    Remaining:
                    <span
                        class="amber--text"
                        v-text="'$ ' + remainingAmount.toFixed(2)"
                    />
                </span>
                <v-text-field
                    :disabled="loading || orderLoading"
                    :min="0.01"
                    :max="99999"
                    label="Amount"
                    type="number"
                    prepend-inner-icon="mdi-currency-usd"
                    v-model="amount"
                    @keyup="limits"
                    @keyup.enter="sendPayment"
                    @blur="limits"
                    style="max-width:150px;"
                ></v-text-field>
            </v-col>
        </v-row>
        <v-row justify="center" align="center" class="my-3">
            <v-col :lg="5">
                <v-btn
                    dark
                    block
                    color="deep-orange"
                    @click="sendPayment"
                    :loading="loading || orderLoading"
                    :disabled="
                        loading ||
                            orderLoading ||
                            !$store.state.cart.isValidCheckout
                    "
                    >{{ paymentBtnTxt }}</v-btn
                >
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        title: String,
        paymentBtnTxt: String,
        types: Array,
        remaining: Number,
        loading: Boolean
    },
    data() {
        return {
            orderLoading: false,
            paymentAmount: Number,
            paymentType: null,
            code: null,

            card: {
                card_holder: null,
                number: null,
                cvc: null,
                exp_date: null
            }
        };
    },

    computed: {
        paymentTypes() {
            if (this.houseAccount) {
                return this.$props.types;
            } else {
                return _.filter(this.$props.types, function(o) {
                    return o.type !== "house-account";
                });
            }
        },
        houseAccountNumber() {
            if (this.houseAccount) {
                return this.$store.state.cart.customer.house_account_number;
            } else {
                return false;
            }
        },
        houseAccount() {
            if (this.$store.state.cart.customer) {
                if (
                    this.$store.state.cart.customer.house_account_status &&
                    this.$store.state.cart.customer.house_account_number &&
                    this.$store.state.cart.customer.house_account_limit > 0
                ) {
                    return true;
                }
            } else {
                return false;
            }
        },
        houseAccountLimit() {
            return parseFloat(
                this.$store.state.cart.customer.house_account_limit
            );
        },
        remainingAmount() {
            if (parseFloat(this.$props.remaining) >= 0) {
                this.amount = parseFloat(this.$props.remaining).toFixed(2);
                return parseFloat(this.$props.remaining);
            } else {
                this.amount = parseFloat(
                    this.$store.state.cart.cart_price
                ).toFixed(2);
                return parseFloat(this.$store.state.cart.cart_price);
            }
        },
        amount: {
            get() {
                return this.paymentAmount;
            },
            set(value) {
                this.paymentAmount = value;
            }
        }
    },

    methods: {
        pay() {
            let payload;

            switch (this.paymentType) {
                case "pos-terminal":
                    payload = {
                        paymentAmount: this.paymentAmount,
                        paymentType: this.paymentType
                    };
                    break;
                case "cash":
                    payload = {
                        paymentAmount: this.paymentAmount,
                        paymentType: this.paymentType
                    };
                    break;
                case "card":
                    payload = {
                        card: this.card,
                        paymentAmount: this.paymentAmount,
                        paymentType: this.paymentType
                    };
                    break;
                case "house-account":
                    payload = {
                        house_account_number: this.houseAccountNumber,
                        paymentAmount: this.paymentAmount,
                        paymentType: this.paymentType
                    };
                    break;
                case "coupon":
                    payload = {
                        code: this.code,
                        paymentType: this.paymentType
                    };
                    break;
                case "giftcard":
                    payload = {
                        code: this.code,
                        paymentAmount: this.paymentAmount,
                        paymentType: this.paymentType
                    };
                    break;
                default:
                    break;
            }

            this.$emit("sendPayment", payload);
            this.limits();
        },
        limits() {
            if (this.paymentType !== "cash") {
                if (this.paymentType === "house-account") {
                    if (
                        this.houseAccountLimit >
                        parseFloat(this.remainingAmount)
                    ) {
                        this.amount = this.remainingAmount;
                    } else if (
                        parseFloat(this.amount) >
                        parseFloat(this.houseAccountLimit)
                    ) {
                        this.amount = this.houseAccountLimit;
                    }
                }
                if (
                    parseFloat(this.amount) > parseFloat(this.remainingAmount)
                ) {
                    this.amount = this.remainingAmount.toFixed(2);
                }
            } else {
                if (parseFloat(this.amount) > 99999) {
                    this.amount = 99999;
                }
            }
        },
        clearState() {
            this.code = null;
            this.card.number = null;
            this.card.card_holder = null;
            this.card.cvc = null;
            this.card.exp_date = null;

            this.limits();
        },
        sendPayment() {
            this.orderLoading = true;
            this.$store.state.cart.paymentLoading = true;

            if (!this.$store.state.cart.order.id) {
                this.submitOrder()
                    .then(() => {
                        this.$store.state.cart.products = [];
                        this.pay();
                    })
                    .finally(() => {
                        this.orderLoading = false;
                    });
            } else {
                this.pay();
                this.orderLoading = false;
            }
        },

        ...mapActions("cart", ["submitOrder"]),

        getIcon() {
            return _.find(this.$props.types, ["type", this.paymentType]).icon;
        }
    },
    beforeDestroy() {
        this.$off("sendPayment", this.sendPayment);
    }
};
</script>
