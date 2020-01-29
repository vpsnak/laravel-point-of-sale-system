<template>
    <ValidationObserver
        v-slot="{ invalid }"
        tag="v-form"
        @submit.prevent="submit"
    >
        <ValidationProvider
            :rules="{
                min_value: '1',
                max_value: '9999',
                required: true,
                regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g
            }"
            v-slot="{ errors, valid }"
            name="Price Amount"
        >
            <v-text-field
                :min="0"
                :max="9999"
                type="number"
                v-model="amount"
                label="Price"
                :error-messages="errors"
                :success="valid"
            ></v-text-field>
        </ValidationProvider>

        <v-btn class="mr-4" type="submit" :disabled="invalid">
            Add to cart
        </v-btn>
    </ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Object
    },
    data() {
        return {
            amount: null
        };
    },
    methods: {
        submit() {
            let giftCard = this.$props.model;

            giftCard.qty = 1;
            giftCard.final_price = this.amount;
            giftCard.price = { amount: this.amount, discount: null };
            giftCard.sku = `giftCard-${giftCard.code}`;

            this.$store.state.cart.products.push(giftCard);

            this.$emit("submit", true);
        }
    },
    beforeDestroy() {
        this.$off("submit");
    }
};
</script>
