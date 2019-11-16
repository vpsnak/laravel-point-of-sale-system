<template>
    <div>
        <shipping @shipping="setShipping" />
        <v-card-actions>
            <span class="title" v-if="shipping.cost"
                >Shipping cost: $ {{ shipping.cost }}</span
            >
            <div class="flex-grow-1"></div>
            <v-btn color="primary" :disabled="!isValid" @click="completeStep">
                Next
                <v-icon small right>mdi-chevron-right</v-icon>
            </v-btn>
        </v-card-actions>
    </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
    data() {
        return {
            shipping: {
                get() {
                    return this.$store.state.cart.shipping;
                },
                set(value) {
                    this.$store.state.cart.shipping = value;
                }
            }
        };
    },
    props: {
        currentStep: Object
    },

    computed: {
        isValid() {
            if (this.$store.state.cart.shipping.method === "retail") {
                return true;
            } else if (this.$store.state.cart.shipping.method === "pickup") {
                return this.$store.state.cart.isValid1;
            } else {
                if (
                    this.$store.state.cart.isValid1 &&
                    this.$store.state.cart.isValid2
                ) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    },

    methods: {
        completeStep() {
            this.$store.dispatch("cart/completeStep");
        },
        prevStep() {
            this.$store.state.cart.currentCheckoutStep--;
        },
        setShipping(value) {
            this.shipping = value;
        }
    }
};
</script>
