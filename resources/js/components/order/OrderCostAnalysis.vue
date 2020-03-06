<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left>mdi-chart-pie</v-icon>
      <span class="subheading">
        Cost analysis
      </span>
    </v-card-title>
    <v-container>
      <v-row justify="center" align="center">
        <v-col>
          <vc-donut
            hasLegend
            legendPlacement="bottom"
            :sections="costSections"
            :size="150"
            :thickness="13"
            :total="Number.parseInt(totalPrice.toFormat('0.00'))"
            :background="bgColor"
          >
            <h2>{{ totalPrice.toFormat() }}</h2>
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
      "order_total_price",
      "order_mdse_price",
      "order_tax_price",
      "delivery_fees_amount"
    ]),

    bgColor() {
      if (this.$vuetify.theme.dark) {
        return "#1e1e1e";
      }
    },

    mdsePrice() {
      return this.parsePrice(this.order_mdse_price);
    },
    taxPrice() {
      return this.parsePrice(this.order_tax_price);
    },
    deliveryFeesPrice() {
      return this.parsePrice(this.delivery_fees_amount);
    },
    totalPrice() {
      return this.parsePrice(this.order_total_price);
    },
    costSections() {
      let sections = [];

      if (!this.mdsePrice.isZero()) {
        sections.push({
          label: `Items: ${this.mdsePrice.toFormat()}`,
          value: Number.parseInt(this.mdsePrice.toFormat("0.00")),
          color: "#003f5c"
        });
      }
      if (!this.deliveryFeesPrice.isZero()) {
        sections.push({
          label: `Shipping: ${this.deliveryFeesPrice.toFormat()}`,
          value: Number.parseInt(this.deliveryFeesPrice.toFormat("0.00")),
          color: "#bc5090"
        });
      }
      if (!this.taxPrice.isZero()) {
        sections.push({
          label: `Tax: ${this.taxPrice.toFormat()}`,
          value: Number.parseInt(this.taxPrice.toFormat("0.00")),
          color: "#ffa600"
        });
      }

      return sections;
    }
  }
};
</script>
