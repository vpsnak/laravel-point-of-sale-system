<template>
    <v-card class="pa-3">
        <div class="d-flex">
            <div class="d-flex align-center justify-center">
                <v-icon class="pr-2">{{ icon }}</v-icon>
                <h4 class="title-2">{{ title }}</h4>
            </div>

            <v-spacer></v-spacer>

            <v-btn-toggle v-model="shippingMethod" mandatory group>
                <v-tooltip bottom color="primary">
                    <template v-slot:activator="{ on }">
                        <v-btn
                            color="primary"
                            icon
                            value="retail"
                            v-on="on"
                            :disabled="order ? true : false"
                        >
                            <v-icon>mdi-cart-arrow-right</v-icon>
                        </v-btn>
                    </template>
                    <span>Cash & Carry</span>
                </v-tooltip>
                <v-tooltip bottom color="warning">
                    <template v-slot:activator="{ on }">
                        <v-btn
                            color="warning"
                            icon
                            value="pickup"
                            :disabled="
                                !$store.state.cart.customer || order
                                    ? true
                                    : false
                            "
                            v-on="on"
                        >
                            <v-icon>mdi-storefront</v-icon>
                        </v-btn>
                    </template>
                    <span>In Store Pickup</span>
                </v-tooltip>
                <v-tooltip bottom color="purple">
                    <template v-slot:activator="{ on }">
                        <v-btn
                            color="purple"
                            icon
                            value="delivery"
                            :disabled="
                                !$store.state.cart.customer || order
                                    ? true
                                    : false
                            "
                            v-on="on"
                        >
                            <v-icon>mdi-truck-delivery</v-icon>
                        </v-btn>
                    </template>
                    <span>Delivery</span>
                </v-tooltip>
            </v-btn-toggle>
        </div>

        <v-divider class="py-1" />

        <customerSearch
            :editable="editable"
            :keywordLength="3"
            class="pa-3"
        ></customerSearch>

        <v-divider class="py-1" />

        <cartProducts :editable="editable"></cartProducts>

        <v-divider />

        <div class="d-flex flex-column">
            <v-row class="d-flex justify-start align-center">
                <v-col cols="4" class="px-5 py-0">
                    <v-label>Cart discount</v-label>
                </v-col>
                <v-col cols="4" class="px-2 py-0">
                    <cartDiscount
                        :product_index="-1"
                        :editable="editable"
                    ></cartDiscount>
                </v-col>
            </v-row>

            <v-divider />

            <cartTotals />

            <div v-if="actions">
                <cartActions :disabled="totalProducts" />
            </div>
        </div>
    </v-card>
</template>

<script>
import { mapActions, mapState, mapMutations } from "vuex";

export default {
    props: {
        title: String,
        icon: String,
        editable: Boolean,
        actions: Boolean
    },
    computed: {
        ...mapState("cart", ["order", "delivery", "method", "cart_products"]),

        shippingMethod: {
            get() {
                return this.method;
            },
            set(value) {
                this.setMethod(value);
                this.setOrderOptions(value);
            }
        },
        totalProducts() {
            return _.size(this.cart_products) ? false : true;
        }
    },

    methods: {
        ...mapMutations("cart", ["setMethod", "setMethodStep"]),

        setOrderOptions(value) {
            switch (value) {
                case "retail":
                    this.setMethodStep({
                        name: "Cash & Carry",
                        icon: "mdi-cart-arrow-right",
                        color: "primary"
                    });
                    break;
                case "pickup":
                    this.setMethodStep({
                        name: "In Store Pickup",
                        icon: "mdi-storefront",
                        color: "warning"
                    });
                    break;
                case "delivery":
                    this.setMethodStep({
                        name: "Delivery",
                        icon: "mdi-truck-delivery",
                        color: "success"
                    });
                    break;
                default:
                    break;
            }
        }
    }
};
</script>
