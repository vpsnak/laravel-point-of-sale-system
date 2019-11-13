<template>
    <div class="d-flex flex-column">
        <div class="d-flex justify-space-between pa-2">
            <span>Sub total w/ discount</span>
            <span>$ {{ subTotalwDiscount.toFixed(2) }}</span>
        </div>

        <v-divider />

        <div class="d-flex justify-space-between pa-2 bb-1">
            <span>Tax</span>
            <span>$ {{ tax.toFixed(2) }}</span>
        </div>

        <v-divider />

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

        <div class="d-flex justify-space-between pa-2">
            <span>Total</span>
            <span>$ {{ total.toFixed(2) }}</span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        order: Array | undefined,
        cart: Object | undefined,
        products: Array | undefined
    },

    computed: {
        shippingCost() {
            if (this.$props.order) {
                return;
            } else if (this.$store.state.cart.shipping.cost) {
                return this.$store.state.cart.shipping.cost;
            }
        },
        subTotalwDiscount() {
            let subtotal = 0;
            if (this.$props.order) {
                this.$props.order.items.forEach(item => {
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
                        this.$props.order.discount_type,
                        this.$props.order.discount_amount
                    );
            } else {
                this.$props.products.forEach(product => {
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
                        this.$props.cart.discount_type,
                        this.$props.cart.discount_amount
                    );
            }
            return subtotal;
        },
        tax() {
            if (this.$props.order) {
                return parseFloat(
                    this.$props.order.total - this.$props.order.subtotal
                );
            } else {
                return parseFloat(
                    (this.subTotalwDiscount *
                        parseFloat(this.$store.state.store.tax.percentage)) /
                        100
                );
            }
        },
        totalDiscount() {
            let subtotalNoDiscount = 0;

            if (this.$props.order) {
                this.$props.order.items.forEach(item => {
                    subtotalNoDiscount += item.price * item.qty;
                });
            } else {
                this.$props.products.forEach(product => {
                    subtotalNoDiscount += product.price.amount * product.qty;
                });
            }

            return subtotalNoDiscount - this.subTotalwDiscount;
        },
        total() {
            if (this.$props.order) {
                return this.$props.order.total;
            } else {
                this.$store.commit(
                    "cart/setCartPrice",
                    this.subTotalwDiscount + this.tax
                );
                return this.subTotalwDiscount + this.tax;
            }
        }
    },
    methods: {
        calcDiscount(price, type, amount) {
            if (type && amount) {
                switch (_.lowerCase(type)) {
                    case "flat":
                        return parseFloat(price) - parseFloat(amount);
                    case "percentage":
                        return (
                            parseFloat(price) -
                            (parseFloat(price) * amount) / 100
                        );
                    default:
                        return price;
                }
            }
            return price;
        }
    }
};
</script>
