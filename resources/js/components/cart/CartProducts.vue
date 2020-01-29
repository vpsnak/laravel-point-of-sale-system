<template>
    <div class="d-flex flex-grow-1" style="height:38vh; overflow-y:auto">
        <v-expansion-panels class="d-block" accordion>
            <v-expansion-panel
                v-for="(product, index) in products"
                :key="index"
            >
                <v-expansion-panel-header class="pa-2" ripple @click.stop>
                    <div class="d-flex justify-space-between">
                        <div class="d-flex flex-column pr-2">
                            <v-img
                                :src="product.photo_url"
                                :lazy-src="product.photo_url"
                                aspect-ratio="1"
                                class="grey lighten-2"
                                width="100%"
                                height="100%"
                                max-width="50"
                                max-height="50"
                            ></v-img>
                        </div>

                        <div class="d-flex flex-column">
                            <span class="subtitle-2">{{ product.name }}</span>

                            <div style="width:100%; max-width:150px;">
                                <v-text-field
                                    prepend-icon="mdi-currency-usd"
                                    single-line
                                    @keyup.esc="revertPrice(index)"
                                    @keyup.enter="setPrice(index, null, true)"
                                    :ref="'priceField' + index"
                                    :min="0"
                                    type="number"
                                    :readonly="!editPrice(index)"
                                    :flat="!editPrice(index)"
                                    :outlined="editPrice(index)"
                                    :solo="!editPrice(index)"
                                    :color="editPrice(index) ? 'yellow' : ''"
                                    :value="parsedPrice(product)"
                                    :hint="
                                        'Original price: $' +
                                            originalPrice(index)
                                    "
                                    @click.stop
                                    dense
                                >
                                </v-text-field>
                            </div>
                        </div>

                        <v-spacer />

                        <div class="d-flex justify-content-center align-center">
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on }">
                                    <v-btn
                                        :color="
                                            editPrice(index) ? 'yellow' : ''
                                        "
                                        :input-value="
                                            editPrice(index) ? true : false
                                        "
                                        @click.stop="toggleEdit(index)"
                                        small
                                        icon
                                        v-on="on"
                                    >
                                        <v-icon small>
                                            mdi-pencil
                                        </v-icon>
                                    </v-btn>
                                </template>
                                <span>Edit price</span>
                            </v-tooltip>
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
                                        @click.stop="viewProductDialog(product)"
                                        v-on="on"
                                    >
                                        <v-icon small class="px-1"
                                            >mdi-eye</v-icon
                                        >
                                    </v-btn>
                                </template>
                                <span>View item</span>
                            </v-tooltip>
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

                            <v-tooltip bottom color="red">
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
                                :product_index="index"
                                :product_price="
                                    parsedPrice(product) * product.qty
                                "
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
            v-if="dialog.show"
            :show="dialog.show"
            :title="dialog.title"
            :titleCloseBtn="dialog.titleCloseBtn"
            :icon="dialog.icon"
            :fullscreen="dialog.fullscreen"
            :width="dialog.width"
            :component="dialog.component"
            :content="dialog.content"
            :model="dialog.model"
            @action="dialogEvent"
            :persistent="dialog.persistent"
        ></interactiveDialog>
    </div>
</template>

<script>
import { mapState } from "vuex";
import Vue from "vue";
Object.defineProperty(Vue.prototype, "Vue", { value: Vue });
export default {
    data() {
        return {
            dialog: {
                show: false,
                width: 600,
                fullscreen: false,
                icon: "",
                title: "",
                titleCloseBtn: false,
                component: "",
                content: "",
                model: "",
                persistent: false
            },
            action: ""
        };
    },
    props: {
        editable: Boolean
    },
    computed: {
        ...mapState("cart", ["discountTypes"]),

        products: {
            get() {
                if (this.$store.state.cart.order) {
                    return this.$store.state.cart.order.items;
                } else {
                    return this.$store.state.cart.products;
                }
            },
            set(value) {
                if (this.$store.state.cart.order) {
                    this.$store.state.cart.order.items = value;
                } else {
                    this.$store.state.cart.products = value;
                }
            }
        }
    },
    methods: {
        getSelectedInput(index) {
            return this.$refs[`priceField${index}`][0];
        },
        setPrice(index, price = null, toggleEdit = false) {
            if (!this.getSelectedInput(index).lazyValue) {
                console.log("nos");
                this.getSelectedInput(index).lazyValue = this.originalPrice(
                    index
                );
            }
            if (price) {
                this.products[index].price.amount = this.products[
                    index
                ].final_price = price;
            } else {
                this.products[index].price.amount = this.products[
                    index
                ].final_price = this.getSelectedInput(index).lazyValue;
            }
            if (toggleEdit) {
                this.toggleEdit(index);
            }
        },
        revertPrice(index) {
            this.$nextTick(() => {
                this.setPrice(index, this.originalPrice(index), true);

                this.getSelectedInput(index).lazyValue = this.originalPrice(
                    index
                );

                console.log(this.getSelectedInput(index));
                this.getSelectedInput(index).blur();
            });
        },
        originalPrice(index) {
            if (_.has(this.products[index], "originalPrice")) {
                return this.products[index].originalPrice;
            } else {
                return this.parsedPrice(this.products[index]);
            }
        },
        editPrice(index) {
            return this.products[index].editPrice;
        },
        toggleEdit(index) {
            if (!_.has(this.products[index], "originalPrice")) {
                Vue.set(
                    this.products[index],
                    "originalPrice",
                    this.parsedPrice(this.products[index])
                );
            }

            Vue.set(
                this.products[index],
                "editPrice",
                !this.products[index].editPrice
            );

            if (this.editPrice(index)) {
                this.$nextTick(() => {
                    this.getSelectedInput(index).focus();
                });
            } else {
                this.setPrice(index);
            }
        },
        dialogEvent(event) {
            this.resetDialog();
        },
        resetDialog() {
            this.dialog = {
                show: false,
                width: 600,
                fullscreen: false,
                title: "",
                titleCloseBtn: false,
                icon: "",
                component: "",
                content: "",
                model: "",
                persistent: false
            };

            this.action = "";
        },
        viewProductDialog(product) {
            this.action = "newItem";

            this.dialog = {
                show: true,
                width: 1000,
                title: "Cart item",
                titleCloseBtn: true,
                icon: "mdi-package-variant",
                component: "product",
                model: product,
                persistent: true
            };
        },
        parsedPrice(product) {
            return (
                parseFloat(product.final_price).toFixed(2) ||
                parseFloat(product.price.toFixed(2))
            );
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
