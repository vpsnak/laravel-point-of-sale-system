<template>
    <v-card outlined>
        <v-card-title>
            <v-icon left>mdi-chart-pie</v-icon>
            <span class="subheading">
                Cost Analysis
            </span>
        </v-card-title>
        <v-container>
            <v-row align="center" justify="center" no-gutters>
                <v-col>
                    <vc-donut
                        hasLegend
                        legendPlacement="bottom"
                        :sections="costSections"
                        :size="150"
                        :thickness="13"
                        :total="order.total"
                        background="#1e1e1e"
                    >
                        <h2>$ {{ precision(order.total) }}</h2>
                        total cost
                    </vc-donut>
                </v-col>
            </v-row>
        </v-container>
    </v-card>
</template>

<script>
import { mapState } from "vuex";
export default {
    mounted() {
        this.sections = [
            {
                label: `Items: $${this.precision(this.subtotal)}`,
                value: this.subtotal,
                color: "#4caf50"
            },
            {
                label: `Shipping: $${this.precision(this.shippingCost)}`,
                value: this.shippingCost,
                color: "purple"
            },
            {
                label: `Tax: $${this.precision(this.tax)}`,
                value: this.tax,
                color: "lightgray"
            }
        ];
    },
    computed: {
        ...mapState("cart", ["order"]),

        costSections() {
            let sections = [];

            if (this.subtotal > 0) {
                sections.push({
                    label: `Items: $${this.precision(this.subtotal)}`,
                    value: this.subtotal,
                    color: "#4caf50"
                });
            }

            if (this.shippingCost > 0) {
                sections.push({
                    label: `Shipping: $${this.precision(this.shippingCost)}`,
                    value: this.shippingCost,
                    color: "purple"
                });
            }
            if (this.tax > 0) {
                sections.push({
                    label: `Tax: $${this.precision(this.tax)}`,
                    value: this.tax,
                    color: "gray"
                });
            }

            return sections;
        },

        tax() {
            return parseFloat(this.order.total - this.order.total_without_tax);
        },
        shippingCost() {
            return parseFloat(this.order.shipping_cost);
        },
        subtotal() {
            return parseFloat(this.order.subtotal);
        }
    },
    methods: {
        precision(float) {
            return float.toFixed(2);
        }
    }
};
</script>
