<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left>mdi-file-chart</v-icon>
      <span class="subheading">
        Payment analysis
      </span>
    </v-card-title>
    <v-container>
      <v-row v-if="loading" justify="center" align="center">
        <v-progress-circular indeterminate color="secondary" />
      </v-row>
      <v-row
        v-else
        justify="space-around"
        align="center"
        dense
        v-for="(section, index) in costSections"
        :key="index"
      >
        <v-col :lg="4" :cols="6" class="text-right">
          <h4>
            {{ section.title }}
          </h4>
        </v-col>
        <v-col :lg="4" :cols="6">
          <h4>
            <i :class="section.class">{{ section.value }}</i>
          </h4>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
  props: {
    ord_id: Number
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
    ...mapState("cart", ["order_id"]),

    orderId() {
      if (this.$props.order_id) {
        return this.$props.order_id;
      } else {
        return this.order_id;
      }
    },
    costSections() {
      this.sections = [];

      if (this.card_pos.greaterThan(this.$price())) {
        this.sections.push({
          title: "Credit Card (POS)",
          value: this.card_pos.toFormat(),
          class: "primary--text"
        });
      }
      if (this.card_keyed.greaterThan(this.$price())) {
        this.sections.push({
          title: "Credit Card (keyed)",
          value: this.card_keyed.toFormat(),
          class: "primary--text"
        });
      }
      if (this.cash.greaterThan(this.$price())) {
        this.sections.push({
          title: "Cash",
          value: this.cash.toFormat(),
          class: "primary--text"
        });
      }
      if (this.house_account.greaterThan(this.$price())) {
        this.sections.push({
          title: "House account",
          value: this.house_account.toFormat(),
          class: "primary--text"
        });
      }
      if (this.giftcard.greaterThan(this.$price())) {
        this.sections.push({
          title: "Giftcards",
          value: this.giftcard.toFormat(),
          class: "primary--text"
        });
      }
      if (this.coupon.greaterThan(this.$price())) {
        this.sections.push({
          title: "Coupons",
          value: this.coupon.toFormat(),
          class: "primary--text"
        });
      }
      this.sections.push({
        title: "Total paid",
        value: this.total_paid.toFormat(),
        class: "success--text"
      });

      return this.sections;
    }
  },
  methods: {
    ...mapActions("requests", ["request"]),

    getPaymentAnalysis() {
      this.loading = true;
      const payload = {
        method: "get",
        url: `orders/${this.orderId}/payment-details`
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
