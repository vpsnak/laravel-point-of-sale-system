<template>
    <div class="d-flex flex-grow-1" style="height:38vh; overflow-y:auto">
        <v-expansion-panels class="d-block" accordion>
            <v-expansion-panel
                v-for="(product, index) in products"
                :key="index"
            >
                <v-expansion-panel-header class="pa-3" ripple @click.stop>
                    <div class="d-flex align-center justify-space-between">
                        <div class="d-flex flex-column">
                            <span class="subtitle-2">{{ product.name }}</span>
                            <span class="body-2">
                                $
                                {{ parseFloat(price(product)).toFixed(2) }}
                            </span>
                        </div>

                        <v-spacer />

                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    v-if="
                                        editable &&
                                            !(
                                                product.sku.startsWith(
                                                    'dummy'
                                                ) ||
                                                product.sku.startsWith(
                                                    'giftCard'
                                                )
                                            )
                                    "
                                    small
                                    icon
                                    @click.stop="viewItem(product)"
                                    v-on="on"
                                >
                                    <v-icon small class="px-1">mdi-eye</v-icon>
                                </v-btn>
                            </template>
                            <span>View product</span>
                        </v-tooltip>
                        <div class="d-flex justify-content-center align-center">
                            <v-btn
                                v-if="editable"
                                small
                                icon
                                @click.stop="decreaseQty(product)"
                            >
                                <v-icon small class="px-1">remove</v-icon>
                            </v-btn>

                            <v-text-field
                                class="px-1"
                                style="max-width:45px;"
                                :disabled="!editable"
                                type="number"
                                label="Qty"
                                v-model="product.qty"
                                :min="1"
                                @click.stop
                                @blur="limits(product)"
                                @keyup="limits(product)"
                            ></v-text-field>

                            <v-btn
                                icon
                                small
                                v-if="editable"
                                @click.stop="increaseQty(product)"
                            >
                                <v-icon small class="px-1">add</v-icon>
                            </v-btn>

                            <v-tooltip bottom>
                                <template v-slot:activator="{ on }">
                                    <v-btn
                                        v-if="editable"
                                        small
                                        icon
                                        @click.stop="removeItem(product)"
                                        color="red"
                                        v-on="on"
                                    >
                                        <v-icon small class="px-1"
                                            >delete</v-icon
                                        >
                                    </v-btn>
                                </template>
                                <span>Remove from cart</span>
                            </v-tooltip>
                        </div>
                    </div>
                </v-expansion-panel-header>
                <v-expansion-panel-content class="pa-3">
                    <v-row no-gutters>
                        <v-col cols="12">
                            <span class="subtitle">
                                SKU:
                                <span
                                    class="amber--text"
                                    v-text="product.sku"
                                />
                            </span>
                        </v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <cartDiscount
                                :model="product"
                                :editable="editable"
                            ></cartDiscount>
                        </v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <v-textarea
                                v-model="product.notes"
                                rows="3"
                                label="Notes"
                                :hint="'For product: ' + product.name"
                                counter
                                no-resize
                                :disabled="!editable"
                            ></v-textarea>
                        </v-col>
                    </v-row>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-expansion-panels>
        <interactiveDialog
            v-if="showViewDialog"
            :show="showViewDialog"
            title="View item"
            :fullscreen="false"
            :width="1000"
            component="product"
            :model="viewProduct"
            @action="result"
            action="newItem"
            cancelBtnTxt="Close"
        ></interactiveDialog>
    </div>
</template>

<script>
import { mapState } from "vuex";
export default {
    data() {
        return {
            showViewDialog: false
        };
    },
    props: {
        products: Array,
        editable: Boolean
    },
    computed: {
        ...mapState("cart", ["discountTypes"])
    },
    methods: {
        price(product) {
            return product.final_price || product.price;
        },
        limits(product) {
            if (product.qty < 1) {
                product.qty = 1;
            }
        },
        decreaseQty(product) {
            this.$store.commit("cart/decreaseProductQty", product);
        },
        increaseQty(product) {
            this.$store.commit("cart/increaseProductQty", product);
        },
        removeItem(product) {
            const index = this.products.indexOf(product);
            this.products.splice(index, 1);
        },
        result(event) {
            this.showViewDialog = false;
        },
        viewItem(product) {
            this.viewProduct = product;
            this.showViewDialog = true;
        }
    }
};
</script>
