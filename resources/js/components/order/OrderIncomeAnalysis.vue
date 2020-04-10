<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left v-text="'mdi-file-chart'" />
      <span class="subheading" v-text="'Earnings analysis'" />
    </v-card-title>
    <v-container>
      <v-row justify="center" align="center">
        <v-progress-circular v-if="loading" indeterminate color="primary" />

        <v-col v-else-if="total_paid.greaterThan(parsePrice())">
          <vc-donut
            hasLegend
            legendPlacement="left"
            :sections="sections"
            :size="150"
            :thickness="13"
            :total="Number(total_paid.toFormat('0.00'))"
            :background="bgColor"
          >
            <h2 v-text="total_paid.toFormat()" />
            <h2 v-text="'total income'" />
          </vc-donut>
        </v-col>
        <v-col v-else cols="auto">
          <v-alert
            type="info"
            border="left"
            colored-border
            :elevation="3"
            dense
          >
            <span v-text="'No earnings'" />
          </v-alert>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState, mapActions } from "vuex";
import { EventBus } from "../../plugins/eventBus";
export default {
  props: {
    orderId: Number
  },

  created() {
    this.getEarningsAnalysis();
  },

  mounted() {
    EventBus.$on("income-analysis-refresh", () => {
      this.getEarningsAnalysis();
    });
  },

  beforeDestroy() {
    EventBus.$off("income-analysis-refresh");
  },

  data() {
    return {
      sections: [],
      loading: false,

      card_pos: null,
      card_keyed: null,
      cash: null,
      house_account: null,
      giftcard: null,
      coupon: null,
      total_paid: null
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

    init() {
      this.card_pos = this.parsePrice();
      this.card_keyed = this.parsePrice();
      this.cash = this.parsePrice();
      this.house_account = this.parsePrice();
      this.giftcard = this.parsePrice();
      this.coupon = this.parsePrice();
      this.total_paid = this.parsePrice();
    },
    setSections() {
      this.sections = [];
      if (this.card_pos.greaterThan(this.$price())) {
        this.sections.push({
          label: `Credit Card (POS): ${this.card_pos.toFormat()}`,
          value: Number(this.card_pos.toFormat("0.00")),
          color: "#003f5c"
        });
      }
      if (this.card_keyed.greaterThan(this.$price())) {
        this.sections.push({
          label: `Credit Card (keyed): ${this.card_keyed.toFormat()}`,
          value: Number(this.card_keyed.toFormat("0.00")),
          color: "#444e86"
        });
      }
      if (this.cash.greaterThan(this.$price())) {
        this.sections.push({
          label: `Cash: ${this.cash.toFormat()}`,
          value: Number(this.cash.toFormat("0.00")),
          color: "#955196"
        });
      }
      if (this.house_account.greaterThan(this.$price())) {
        this.sections.push({
          label: `House account: ${this.house_account.toFormat()}`,
          value: Number(this.house_account.toFormat("0.00")),
          color: "#dd5182"
        });
      }
      if (this.giftcard.greaterThan(this.$price())) {
        this.sections.push({
          label: `Giftcards: ${this.giftcard.toFormat()}`,
          value: Number(this.giftcard.toFormat("0.00")),
          color: "#ff6e54"
        });
      }
      if (this.coupon.greaterThan(this.$price())) {
        this.sections.push({
          label: `Coupons: ${this.coupon.toFormat()}`,
          value: Number(this.coupon.toFormat("0.00")),
          color: "#ffa600"
        });
      }

      return this.sections;
    },
    getEarningsAnalysis() {
      this.loading = true;
      this.init();
      const payload = {
        method: "get",
        url: `orders/${this.$props.orderId}/income-details`
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
