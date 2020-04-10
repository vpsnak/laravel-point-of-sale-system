<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left v-text="'mdi-chart-pie'" />
      <span class="subheading" v-text="'Cost analysis'" />
    </v-card-title>
    <v-container>
      <v-row justify="center" align="center">
        <v-col>
          <vc-donut
            hasLegend
            legendPlacement="right"
            :sections="costSections"
            :size="150"
            :thickness="13"
            :total="Number(totalPrice.toFormat('0.00'))"
            :background="bgColor"
          >
            <h2 v-text="totalPrice.toFormat()" />
            <h2 v-text="'total cost'" />
          </vc-donut>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState } from "vuex";
export default {
  props: {
    orderPrices: Object
  },

  computed: {
    ...mapState("cart", [
      "order_total_price",
      "order_mdse_price",
      "order_tax_price",
      "delivery_fees_price"
    ]),

    bgColor() {
      if (this.$vuetify.theme.dark) {
        return "#1e1e1e";
      }
    },
    mdsePrice() {
      if (this.$props.orderPrices) {
        return this.parsePrice(this.$props.orderPrices.mdsePrice);
      } else {
        return this.parsePrice(this.order_mdse_price);
      }
    },
    taxPrice() {
      if (this.$props.orderPrices) {
        return this.parsePrice(this.$props.orderPrices.taxPrice);
      } else {
        return this.parsePrice(this.order_tax_price);
      }
    },
    deliveryFeesPrice() {
      if (this.$props.orderPrices) {
        return this.parsePrice(this.$props.orderPrices.deliveryFeesPrice);
      } else {
        return this.parsePrice(this.delivery_fees_price);
      }
    },
    totalPrice() {
      if (this.$props.orderPrices) {
        return this.parsePrice(this.$props.orderPrices.totalPrice);
      } else {
        return this.parsePrice(this.order_total_price);
      }
    },
    costSections() {
      let sections = [];
      if (!this.mdsePrice.isZero()) {
        sections.push({
          label: `Items: ${this.mdsePrice.toFormat()}`,
          value: Number(this.mdsePrice.toFormat("0.00")),
          color: "#003f5c"
        });
      }
      if (!this.deliveryFeesPrice.isZero()) {
        sections.push({
          label: `Delivery: ${this.deliveryFeesPrice.toFormat()}`,
          value: Number(this.deliveryFeesPrice.toFormat("0.00")),
          color: "#bc5090"
        });
      }
      if (!this.taxPrice.isZero()) {
        sections.push({
          label: `Tax: ${this.taxPrice.toFormat()}`,
          value: Number(this.taxPrice.toFormat("0.00")),
          color: "#ffa600"
        });
      }
      return sections;
    }
  }
};
</script>
