<template>
    <div>
        <payment
            :order_id="orderId"
            history
            actions
            @amountPending="showCompleteBtn"
        />
        <v-card-actions>
            <v-btn color="grey" @click="prevStep()" v-if="orderId === 0">
                <v-icon small left>mdi-chevron-left</v-icon>Back
            </v-btn>
            <div class="flex-grow-1"></div>
            <v-btn
                v-if="completed && !$store.state.cart.refundLoading"
                color="primary"
                @click="completeStep"
                :loading="loading"
                :disabled="loading"
                >Complete order</v-btn
            >
        </v-card-actions>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
    props: {
        currentStep: Object
    },
    data() {
        return {
            completed: false,
            loading: false
        };
    },
    computed: {
        ...mapState("cart", ["order"]),
        orderId() {
            return this.order ? this.order.id : 0;
        }
    },
    methods: {
        showCompleteBtn(event) {
            if (event > 0 || event === undefined) {
                this.completed = false;
            } else {
                this.completed = true;
            }
        },
        completeStep() {
            this.loading = true;
            let payload = {
                model: "orders",
                data: {
                    id: this.order.id,
                    status: "complete"
                },
                mutation: "cart/setOrder"
            };

            this.create(payload).then(response => {
                this.$store.dispatch("cart/completeStep").then(() => {
                    this.loading = false;
                });
            });
        },
        prevStep() {
            this.$store.state.cart.currentCheckoutStep--;
        },

        ...mapActions(["create"])
    }
};
</script>
