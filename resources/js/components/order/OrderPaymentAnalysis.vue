<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left>mdi-file-chart</v-icon>
      <span class="subheading">
        Payment analysis
      </span>
    </v-card-title>
    <v-container>
      <v-row justify="center" align="center">
        <v-progress-circular v-if="loading" indeterminate color="secondary" />

        <v-col v-else-if="total_paid.greaterThan($price({ amount: 0 }))">
          <vc-donut
            hasLegend
            legendPlacement="left"
            :sections="sections"
            :size="150"
            :thickness="13"
            :total="Number(total_paid.toFormat('0.00'))"
            :background="bgColor"
          >
            <h2>{{ total_paid.toFormat() }}</h2>
            <h2>total paid</h2>
          </vc-donut>
        </v-col>
        <v-col v-else cols="auto">
          No payments have been made
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
  props: {
    orderId: Number
  },

  created() {
    this.getPaymentAnalysis();
  },

  data() {
    return {
      sections: [],
      loading: false,

      card_pos: this.$price(),
      card_keyed: this.$price(),
      cash: this.$price(),
      house_account: this.$price(),
      giftcard: this.$price(),
      coupon: this.$price(),
      total_paid: this.$price()
    };
  },

  computed: {
    bgColor() {
      if (this.$vuetify.theme.dark) {
        return "#1e1e1e";
      }
    }
  },
  methods: {
    ...mapActions("requests", ["request"]),

    setSections() {
      this.sections = [];
      if (this.card_pos.greaterThan(this.$price())) {
        this.sections.push({
          label: "Credit Card (POS)",
          value: Number(this.card_pos.toFormat("0.00")),
          color: "#003f5c"
        });
      }
      if (this.card_keyed.greaterThan(this.$price())) {
        this.sections.push({
          label: "Credit Card (keyed)",
          value: Number(this.card_keyed.toFormat("0.00")),
          color: "#444e86"
        });
      }
      if (this.cash.greaterThan(this.$price())) {
        this.sections.push({
          label: "Cash",
          value: Number(this.cash.toFormat("0.00")),
          color: "#955196"
        });
      }
      if (this.house_account.greaterThan(this.$price())) {
        this.sections.push({
          label: "House account",
          value: Number(this.house_account.toFormat("0.00")),
          color: "#dd5182"
        });
      }
      if (this.giftcard.greaterThan(this.$price())) {
        this.sections.push({
          label: "Giftcards",
          value: Number(this.giftcard.toFormat("0.00")),
          color: "#ff6e54"
        });
      }
      if (this.coupon.greaterThan(this.$price())) {
        this.sections.push({
          label: "Coupons",
          value: Number(this.coupon.toFormat("0.00")),
          color: "#ffa600"
        });
      }

      return this.sections;
    },
    getPaymentAnalysis() {
      this.loading = true;
      const payload = {
        method: "get",
        url: `orders/${this.$props.orderId}/payment-details`
      };

      this.request(payload)
        .then(response => {
          this.card_pos = this.card_pos.add(this.$price(response.card_pos));
          this.card_keyed = this.card_keyed.add(
            this.$price(response.card_keyed)
          );
          this.cash = this.cash.add(this.$price(response.cash));
          this.house_account = this.house_account.add(
            this.$price(response.house_account)
          );
          this.giftcard = this.giftcard.add(this.$price(response.giftcard));
          this.coupon = this.coupon.add(this.$price(response.coupon));
          this.total_paid = this.total_paid.add(
            this.$price(response.total_paid)
          );

          this.setSections();
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
