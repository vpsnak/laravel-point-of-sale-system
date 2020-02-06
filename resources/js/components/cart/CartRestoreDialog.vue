<template>
    <div>
        <v-chip-group multiple column active-class="primary--text">
            <v-chip
                class="d-flex justify-center pa-2"
                v-for="cartOnHold in cartsOnHold"
                :key="cartOnHold.id"
                close
                @click="(selectedCart = cartOnHold), restoreCart()"
                @click:close="(selectedCart = cartOnHold), removeCart()"
            >
                <span>
                    {{ cartOnHold.name }}
                    <v-icon left>mdi-cartOnHold</v-icon>
                </span>
            </v-chip>
        </v-chip-group>
        <v-divider></v-divider>

        <v-card-actions>
            <div class="flex-grow-1"></div>
            <v-btn color="primary" text @click="close">Close</v-btn>
        </v-card-actions>
    </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
    props: {
        show: Boolean
    },

    data() {
        return {
            onHold: [],
            cart_replace_prompt: false,
            selectedCart: {}
        };
    },

    computed: {
        cartReplacePrompt: {
            get() {
                return this.cart_replace_prompt;
            },
            set(value) {
                this.cart_replace_prompt = value;
            }
        },
        cartsOnHold: {
            get() {
                return this.onHold;
            },
            set(value) {
                this.onHold = value;
            }
        }
    },

    mounted() {
        this.getCartsOnHold();
    },

    methods: {
        confirmation(event) {
            if (event) {
                this.loadCart();
            }

            this.cartReplacePrompt = false;
        },
        getCartsOnHold() {
            let payload = {
                model: "carts"
            };
            this.$store.dispatch("getAll", payload).then(response => {
                this.cartsOnHold = response;
            });
        },
        close() {
            this.$emit("close");
            this.$store.state.cartRestoreDialog = false;
        },
        restoreCart() {
            let cart = JSON.parse(this.selectedCart.cart).products;

            if (_.size(this.$store.state.cart.products)) {
                this.cartReplacePrompt = true;
            } else {
                this.loadCart();
            }
        },
        loadCart() {
            let cart = JSON.parse(this.selectedCart.cart).products;
            this.$store.state.cart.products = cart.products;
            this.$store.state.cart.shipping.notes = cart.shipping.notes;
            this.$store.state.cart.discount_type = cart.discount_type;
            this.$store.state.cart.discount_amount = cart.discount_amount;

            this.getOne({
                model: "customers",
                data: {
                    id: JSON.parse(this.selectedCart.cart).customer_id
                },
                mutation: "cart/setCustomer"
            }).then(response => {
                this.removeCart();
                this.close();
            });
        },
        removeCart() {
            return this.delete({
                model: "carts",
                id: this.selectedCart.id
            }).then(() => {
                this.cartsOnHold = this.cartsOnHold.filter(cart => {
                    return cart.id !== this.selectedCart.id;
                });
            });
        },
        ...mapActions({
            getOne: "getOne",
            delete: "delete"
        })
    },

    beforeDestroy() {
        this.$off("close");
    }
};
</script>
