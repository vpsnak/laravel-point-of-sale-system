<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left>mdi-chart-pie</v-icon>
      <span class="subheading">
        Order cost analysis
      </span>
    </v-card-title>
    <v-container>
      <v-row justify="center" align="center">
        <v-col>
          <vc-donut
            hasLegend
            legendPlacement="left"
            :sections="costSections"
            :size="150"
            :thickness="13"
            :total="order_total"
            :background="bgColor"
          >
            <h2>$ {{ order_total }}</h2>
            <h2>total cost</h2>
          </vc-donut>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState } from "vuex";
export default {
  computed: {
    ...mapState("cart", [
      "order_id",
      "order_status",
      "order_total",
      "order_total_without_tax",
      "order_total_tax",
      "order_total_item_cost",
      "order_change",
      "order_remaining",
      "order_notes",
      "order_billing_address",
      "order_delivery_address",
      "order_delivery_store_pickup",
      "order_delivery_store_pickup",
      "shipping_cost",
      "refresh_order"
    ]),

    bgColor() {
      if (this.$vuetify.theme.dark) {
        return "#1e1e1e";
      }
    },

    costSections() {
      let sections = [];

      if (this.order_total_item_cost > 0) {
        sections.push({
          label: `Items: $${this.order_total_item_cost}`,
          value: this.order_total_item_cost,
          color: "#003f5c"
        });
      }
      if (this.shipping_cost > 0) {
        sections.push({
          label: `Shipping: $${this.shipping_cost}`,
          value: this.shipping_cost,
          color: "#bc5090"
        });
      }
      if (this.order_total_tax > 0) {
        sections.push({
          label: `Tax: $${this.order_total_tax}`,
          value: this.order_total_tax,
          color: "#ffa600"
        });
      }

      return sections;
    }
  }
};
</script>
