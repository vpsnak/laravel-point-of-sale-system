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
            :total="order_total"
            background="#1e1e1e"
          >
            <h2>$ {{ order_total }}</h2>
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
        label: `Items: $${this.order_total_without_tax}`,
        value: this.order_total_without_tax,
        color: "#4caf50"
      },
      {
        label: `Shipping: $${this.shipping_cost}`,
        value: this.shipping_cost,
        color: "purple"
      },
      {
        label: `Tax: $${this.order_total_tax}`,
        value: this.order_total_tax,
        color: "lightgray"
      }
    ];
  },
  computed: {
    ...mapState("cart", [
      "order_id",
      "order_status",
      "order_total",
      "order_total_without_tax",
      "order_total_tax",
      "order_change",
      "order_remaining",
      "order_notes",
      "order_billing_address",
      "order_delivery_address",
      "order_delivery_store_pickup",
      "order_delivery_store_pickup",
      "shipping_cost"
    ]),

    costSections() {
      let sections = [];

      if (this.order_total_without_tax > 0) {
        sections.push({
          label: `Items: $${this.order_total_without_tax}`,
          value: this.order_total_without_tax,
          color: "#4caf50"
        });
      }

      if (this.shipping_cost > 0) {
        sections.push({
          label: `Shipping: $${this.shipping_cost}`,
          value: this.shipping_cost,
          color: "purple"
        });
      }
      if (this.order_total_tax > 0) {
        sections.push({
          label: `Tax: $${this.order_total_tax}`,
          value: this.order_total_tax,
          color: "gray"
        });
      }

      return sections;
    }
  }
};
</script>
