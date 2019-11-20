<template>
    <div class="d-flex" :key="$props.model.id || 1">
        <v-col cols="6" class="pa-0 pr-2">
            <v-select
                v-if="editable"
                @change="amount = amount"
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
            <ValidationObserver v-slot="{ valid, invalid }" ref="checkoutObs">
                <input
                    type="hidden"
                    :value="($store.state.cart.isValidCheckout = valid)"
                />
                <ValidationProvider
                    :rules="'between:0,' + max.toFixed(2)"
                    v-slot="{ invalid }"
                    name="Discount amount"
                >
                    <v-text-field
                        v-model="amount"
                        type="number"
                        label="Amount"
                        :min="0"
                        :error-messages="invalid ? 'Invalid amount' : undefined"
                        :success="valid"
                        :disabled="!editable"
                    ></v-text-field>
                </ValidationProvider>
            </ValidationObserver>
        </v-col>
    </div>
</template>

<script>
import { mapState } from "vuex";
import { mapGetters } from "vuex";

export default {
    props: {
        model: Array | Object,
        editable: Boolean
    },
    computed: {
        ...mapState("cart", ["discountTypes"]),
        max() {
            switch (this.discountType) {
                case "Flat":
                    if (this.$props.model.cart_price) {
                        return (
                            parseFloat(this.$props.model.cart_price) +
                            parseFloat(this.$props.model.discount_amount)
                        );
                    } else if (this.$props.model.final_price) {
                        return (
                            parseFloat(this.$props.model.final_price) *
                            parseInt(this.$props.model.qty)
                        );
                    } else {
                        return 0;
                    }
                case "Percentage":
                    return 100;
                default:
                    return 0;
            }
        },
        discountType: {
            get() {
                return this.$props.model.discount_type
                    ? this.$props.model.discount_type
                    : "None";
            },
            set(value) {
                this.$set(this.$props.model, "discount_type", value);

                if (value === "None") {
                    this.amount = 0;
                }
            }
        },
        amount: {
            get() {
                return this.$props.model.discount_amount;
            },
            set(value) {
                let model = this.$props.model;
                this.$set(
                    model,
                    "discount_amount",
                    value ? parseFloat(value) : 0
                );
                if (value <= this.max) {
                    this.$store.commit("cart/setDiscount", model);
                }
            }
        }
    }
};
</script>
