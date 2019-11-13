<template>
    <div>
        <v-divider />

        <v-btn block class="my-2" @click.stop="checkout" :disabled="disabled"
            >Checkout</v-btn
        >

        <v-divider />

        <div class="d-flex align-center justify-center pa-2">
            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        icon
                        @click="showRestoreOnHoldCartDialog"
                        class="flex-grow-1"
                        tile
                        color="green"
                        v-on="on"
                        :disabled="!cartsOnHoldSize"
                    >
                        <v-icon>fa-recycle</v-icon>
                        <v-badge
                            overlap
                            color="purple"
                            style="position: absolute; top: 0;right:38%;"
                        >
                            <template v-slot:badge>
                                <span>{{ cartsOnHoldSize }}</span>
                            </template>
                        </v-badge>
                    </v-btn>
                </template>
                <span>Restore cart</span>
            </v-tooltip>

            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        icon
                        :disabled="disabled"
                        @click="holdCart"
                        class="flex-grow-1"
                        tile
                        color="yellow"
                        v-on="on"
                    >
                        <v-icon>pause</v-icon>
                    </v-btn>
                </template>
                <span>Hold current cart</span>
            </v-tooltip>

            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        icon
                        @click.stop="emptyCart(true)"
                        :disabled="disabled"
                        class="flex-grow-1"
                        tile
                        color="red"
                        v-on="on"
                    >
                        <v-icon>delete</v-icon>
                    </v-btn>
                </template>
                <span>Empty current cart</span>
            </v-tooltip>
        </div>
        <interactiveDialog
            v-if="emptyCartConfirmationDialog"
            :show="emptyCartConfirmationDialog"
            action="confirmation"
            title="Empty cart"
            content="<p>Are you sure you want to empty the cart?</p>"
            @action="emptyConfirmation"
            actions
            persistent
        />

        <checkoutDialog :show="checkoutDialog" />
        <cartRestoreDialog
            :show="cartRestoreDialog"
            :key="cartsOnHoldSize"
            @close="getCartsOnHoldSize"
        />
    </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
    data() {
        return {
            empty_cart_confirmation_dialog: false,
            carts_on_hold_size: 0
        };
    },
    props: {
        disabled: Boolean
    },

    mounted() {
        this.getCartsOnHoldSize();
    },

    computed: {
        emptyCartConfirmationDialog: {
            get() {
                return this.empty_cart_confirmation_dialog;
            },
            set(value) {
                this.empty_cart_confirmation_dialog = value;
            }
        },
        cartsOnHoldSize: {
            get() {
                return this.carts_on_hold_size;
            },
            set(value) {
                this.carts_on_hold_size = value;
            }
        },
        cart: {
            get() {
                return this.$store.state.cart;
            }
        },
        checkoutDialog: {
            get() {
                return this.$store.state.checkoutDialog;
            },
            set(value) {
                this.$store.state.checkoutDialog = value;
            }
        },
        cartRestoreDialog: {
            get() {
                return this.$store.state.cartRestoreDialog;
            },
            set(value) {
                this.$store.state.cartRestoreDialog = value;
            }
        }
    },

    methods: {
        checkout() {
            this.$store.commit("cart/setOrder", undefined);
            this.checkoutDialog = true;
        },
        emptyConfirmation(event) {
            if (event) {
                this.$store.commit("cart/resetState");
            }
            this.emptyCartConfirmationDialog = false;
        },
        emptyCart(showPrompt) {
            if (showPrompt) {
                this.emptyCartConfirmationDialog = true;
            } else {
                this.$store.commit("cart/resetState");
            }
        },
        holdCart() {
            let product_count = Object.keys(this.cart.products).length;
            let payload = {
                model: "carts",
                data: {
                    cash_register_id: this.$store.state.cashRegister.id,
                    cart: {
                        products: this.cart,
                        customer_id: this.$store.state.cart.customer
                            ? this.$store.state.cart.customer.id
                            : null
                    },
                    discount_type: this.$store.state.cart.discount_type,
                    discount_amount: this.$store.state.cart.discount_amount,
                    shipping: { notes: this.$store.state.cart.shipping.notes },
                    product_count: product_count,
                    total_price: this.cart.cart_price
                }
            };
            this.create(payload).then(() => {
                this.getCartsOnHoldSize();
                this.emptyCart(false);

                this.$store.commit("setNotification", {
                    msg: "Cart added on hold list",
                    type: "info"
                });
            });
        },
        showRestoreOnHoldCartDialog() {
            this.cartRestoreDialog = true;
        },
        getCartsOnHoldSize() {
            let payload = {
                model: "carts"
            };
            this.$store.dispatch("getAll", payload).then(response => {
                this.cartsOnHoldSize = _.size(response);
            });
        },
        ...mapActions({
            getAll: "getAll",
            getOne: "getOne",
            create: "create",
            delete: "delete"
        }),
        ...mapActions("cart", {
            submitOrder: "submitOrder"
        })
    }
};
</script>
