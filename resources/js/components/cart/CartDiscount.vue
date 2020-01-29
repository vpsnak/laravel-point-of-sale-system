<template>
    <ValidationObserver ref="checkoutObs">
        <div class="d-flex" :key="$props.product_index">
            <v-col cols="6" class="pa-0 pr-2">
                <v-select
                    @change="validate"
                    v-if="editable"
                    v-model="discountType"
                    :items="discountTypes"
                    label="Discount"
                    item-text="label"
                    item-value="value"
                ></v-select>
                <v-text-field
                    v-else-if="!editable"
                    :value="discountType"
                    label="Discount"
                    disabled
                ></v-text-field>
            </v-col>

            <v-col
                cols="6"
                class="pa-0 pl-2"
                v-if="discountType && discountType !== 'None'"
            >
                <ValidationProvider
                    v-slot="{ valid }"
                    :rules="`between:${0},${max}`"
                    name="Discount amount"
                >
                    <v-text-field
                        @blur="limits"
                        @change="validate"
                        @keyup="validate"
                        v-model="discountAmount"
                        type="number"
                        label="Amount"
                        :min="0"
                        :max="max.toFixed(2)"
                        :error-messages="!valid ? 'Invalid amount' : undefined"
                        :success="valid"
                        :disabled="!editable"
                    ></v-text-field>
                </ValidationProvider>
            </v-col>
        </div>
    </ValidationObserver>
</template>

<script>
import { mapState } from "vuex";
import { mapGetters } from "vuex";

export default {
    props: {
        product_price: Number,
        product_index: Number,
        editable: Boolean
    },
    watch: {
        product_price() {
            this.validate();
        }
    },
    methods: {
        validate() {
            this.setDiscount(this.discountAmount);
            this.$store.commit("cart/isValidDiscount");
        },
        limits() {
            if (
                this.discountType === "Percentage" ||
                this.discountType === "percentage"
            ) {
                if (this.discountAmount > 99) {
                    this.discountAmount = "99";
                }
            }

            if (this.discountAmount) {
                let digits = this.discountAmount.split(".");

                if (digits.length === 2 && digits[1].length > 2)
                    this.discountAmount = parseFloat(
                        this.discountAmount
                    ).toFixed(2);
            }

            this.validate();
        },
        setDiscount(value) {
            if (this.$props.product_index === -1) {
                this.order.discount_amount = value;

                this.$refs.checkoutObs.validate().then(result => {
                    this.order.discount_error = !result;
                    return;
                });
            } else {
                Vue.set(this.product, "discount_amount", value);

                this.$refs.checkoutObs.validate().then(result => {
                    Vue.set(this.product, "discount_error", !result);
                    return;
                });
            }
        }
    },
    computed: {
        ...mapState("cart", ["discountTypes"]),
        order: {
            get() {
                if (this.$store.state.cart.order) {
                    return this.$store.state.cart.order;
                } else {
                    return this.$store.state.cart;
                }
            },
            set(value) {
                if (this.$store.state.cart.order) {
                    this.$store.state.cart.order = value;
                } else {
                    this.$store.state.cart = value;
                }
            }
        },
        product: {
            get() {
                if (this.order.items) {
                    return this.order.items[this.$props.product_index];
                } else {
                    return this.order.products[this.$props.product_index];
                }
            },
            set(value) {
                if (this.order.items) {
                    this.order.items[this.$props.product_index] = value;
                } else {
                    this.order.products[this.$props.product_index] = value;
                }
            }
        },
        max() {
            switch (this.discountType) {
                case "flat":
                case "Flat":
                    if (this.$props.product_index === -1) {
                        let max = (
                            parseFloat(this.order.cart_price) +
                            parseFloat(this.order.discount_amount)
                        ).toFixed(2);

                        return parseFloat(max - 0.01);
                    } else {
                        return parseFloat(this.$props.product_price);
                    }
                case "percentage":
                case "Percentage":
                    return 99;
                default:
                    return 0;
            }
        },
        discountType: {
            get() {
                if (this.$props.product_index === -1) {
                    return this.order.discount_type;
                } else {
                    return this.product.discount_type;
                }
            },
            set(value) {
                if (this.$props.product_index === -1) {
                    this.order.discount_type = value;
                } else {
                    Vue.set(this.product, "discount_type", value);
                }

                this.setDiscount(this.discountAmount);
            }
        },
        discountAmount: {
            get() {
                if (this.$props.product_index === -1) {
                    return this.order.discount_amount;
                } else {
                    return this.product.discount_amount;
                }
            },
            set(value) {
                this.setDiscount(value);
            }
        }
    }
};
</script>
