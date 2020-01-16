<template>
    <div class="d-flex flex-column">
        <div class="d-flex justify-space-between pa-2">
            <span>Sub total w/ discount</span>
            <span>$ {{ subTotalwDiscount.toFixed(2) }}</span>
        </div>

        <v-divider v-if="totalDiscount" />

        <div class="d-flex justify-space-between pa-2" v-if="totalDiscount">
            <span>Total discount</span>
            <span>$ {{ totalDiscount.toFixed(2) }}</span>
        </div>

        <v-divider />

        <div class="d-flex justify-space-between pa-2" v-if="shippingCost">
            <span>Shipping cost</span>
            <span>$ {{ shippingCost.toFixed(2) }}</span>
        </div>

        <v-divider />

        <div class="d-flex justify-space-between pa-2 bb-1">
            <span>Tax</span>
            <span>$ {{ tax.toFixed(2) }}</span>
        </div>

        <v-divider />

        <div class="d-flex justify-space-between pa-2">
            <span>Total</span>
            <span>$ {{ total.toFixed(2) }}</span>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        cart: {
            get() {
                return this.$store.state.cart;
            },
            set(value) {
                this.$store.state.cart = value;
            }
        },
        order: {
            get() {
                return this.cart.order;
            },
            set(value) {
                this.cart.order = value;
            }
        },
        products: {
            get() {
                if (this.order) {
                    return this.order.items;
                } else {
                    return this.cart.products;
                }
            },
            set(value) {
                if (this.order) {
                    this.order.items = value;
                } else {
                    this.cart.products = value;
                }
            }
        },
        customer() {
            return this.cart.customer;
        },
        shippingCost() {
            if (this.order) {
                return parseFloat(this.order.shipping_cost);
            } else if (this.$store.state.cart.shipping.cost) {
                return parseFloat(this.cart.shipping.cost);
            } else {
                return 0;
            }
        },
        subTotalwDiscount() {
            let subtotal = 0;
            if (this.order) {
                this.order.items.forEach(item => {
                    subtotal += this.calcDiscount(
                        item.price * item.qty,
                        item.discount_type,
                        item.discount_amount
                    );
                });

                subtotal -=
                    subtotal -
                    this.calcDiscount(
                        subtotal,
                        this.order.discount_type,
                        this.order.discount_amount
                    );
            } else {
                this.products.forEach(product => {
                    subtotal += this.calcDiscount(
                        product.price.amount * product.qty,
                        product.discount_type,
                        product.discount_amount
                    );
                });

                subtotal -=
                    subtotal -
                    this.calcDiscount(
                        subtotal,
                        this.cart.discount_type,
                        this.cart.discount_amount
                    );
            }
            return parseFloat(subtotal);
        },
        tax() {
            if (this.customer) {
                if (this.customer.no_tax) {
                    return 0;
                }
            }

            if (this.order) {
                return parseFloat(
                    this.order.total - this.order.total_without_tax
                );
            } else {
                return parseFloat(
                    ((this.subTotalwDiscount + this.shippingCost) *
                        parseFloat(this.$store.state.store.tax.percentage)) /
                        100
                );
            }
        },
        totalDiscount() {
            let subtotalNoDiscount = 0;

            if (this.order) {
                this.order.items.forEach(item => {
                    subtotalNoDiscount += item.price * item.qty;
                });
            } else {
                this.cart.products.forEach(product => {
                    subtotalNoDiscount += product.price.amount * product.qty;
                });
            }

            return subtotalNoDiscount - this.subTotalwDiscount;
        },
        total() {
            Vue.set(
                this.cart,
                "cart_price",
                this.subTotalwDiscount + this.tax + this.shippingCost
            );

            if (parseFloat(this.cart.cart_price) > 0) {
                this.cart.isValidCheckout = true;
            } else {
                this.cart.isValidCheckout = false;
            }

            return this.subTotalwDiscount + this.tax + this.shippingCost;
        }
    },
    methods: {
        calcDiscount(price, type, amount) {
            if (type && amount) {
                switch (_.lowerCase(type)) {
                    case "flat":
                        return (
                            parseFloat(price).toFixed(2) -
                            parseFloat(amount).toFixed(2)
                        );
                    case "percentage":
                        return (
                            parseFloat(price) -
                            (parseFloat(price) * amount) / 100
                        ).toFixed(2);
                    default:
                        return price;
                }
            }
            return price;
        }
    }
};
</script>
