<template>
  <ValidationObserver
    ref="refund-form"
    v-slot="{ valid }"
    tag="v-form"
    @submit.prevent="submitRefund()"
  >
    <v-container>
      <v-row align="center" justify="center">
        <v-progress-circular
          v-if="payment_types_loading"
          indeterminate
          color="secondary"
        ></v-progress-circular>
        <v-col :cols="12" v-else>
          <v-btn-toggle
            v-model="refund.method"
            mandatory
            @change="clearState()"
          >
            <v-btn
              v-for="payment_type in payment_types"
              :key="payment_type.id"
              :value="payment_type.type"
            >
              <v-icon class="pr-2">{{ payment_type.icon }}</v-icon>
              {{ payment_type.name }}
            </v-btn>
          </v-btn-toggle>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" v-show="!payment_types_loading">
        <v-col cols="auto">
          <h4 class="amber--text">Max amount: ${{}}</h4>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" v-show="!payment_types_loading">
        <v-col cols="auto">
          <v-text-field
            v-model="refund.amount"
            type="number"
            label="Amount"
            hint="Max refundable amount"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row justify="center" align="center" v-show="!payment_types_loading">
        <v-col cols="auto">
          <v-btn :disabled="!valid" type="submit" text>
            Refund
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  mounted() {
    this.getPaymentTypes();
  },

  beforeDestroy() {
    EventBus.$off("");
  },

  data() {
    return {
      payment_type: null,
      payment_types: [],
      payment_types_loading: false,
      refund: {
        amount: null
      }
    };
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "order_total_paid",
      "order_total",
      "order_status"
    ]),

    maxRefund() {
      if (this.order_status === "paid") {
        return this.order_total;
      } else {
        return this.order_total_paid;
      }
    },
    getIcon() {
      return _.find(this.payment_types, ["type", this.payment_type]).icon;
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("requests", ["request"]),

    submitRefund() {
      this.loading = true;

      let payload = {
        method: "post",
        url: "payments/unlinked-refund",
        mutations: [],
        data: {
          order_id: this.order_id,
          method: this.method,
          amount: this.amount
        }
      };
      switch (this.method) {
        case "giftcard-existing":
          payload.data.existing_gc_code = this.gc_code;
          break;
        case "giftcard-new":
          payload.data.giftcard = {
            code: this.gc_code,
            name: this.gc_name
          };
          break;
        default:
          this.loading = false;
          return;
      }
      this.request(payload)
        .then(response => {
          console.log(response);
        })
        .finally(() => {
          this.loading = false;
        });
    },

    getPaymentTypes() {
      this.payment_types_loading = true;
      const payload = {
        method: "get",
        url: "refund-types"
      };

      this.request(payload)
        .then(response => {
          this.payment_types = response;
        })
        .catch()
        .finally(() => {
          this.payment_types_loading = false;
        });
    },
    clearState() {}
  }
};
</script>
