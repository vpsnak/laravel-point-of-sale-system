<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>mdi-currency-usd</v-icon>
      <span class="subheading">
        Transaction history
      </span>
    </v-card-title>

    <v-data-table
      no-data-text="No transactions have been made"
      dense
      height="15vh"
      fixed-header
      :headers="headers"
      :items="payments"
      class="elevation-1"
      disable-pagination
      disable-filtering
      hide-default-footer
      :loading="false"
    >
      <template v-slot:item.status="{ item }">
        <b :class="statusColor(item.status)">
          {{ parseStatus(item.status) }}
        </b>
      </template>

      <template v-slot:item.price="{ item }">
        <b :class="statusColor(item.status)">
          {{ parsePrice(item.price).toFormat() }}
        </b>
      </template>

      <template v-slot:item.change_price="{ item }">
        <b :class="changePriceColor">
          {{ changePrice(item.change_price).toFormat() }}
        </b>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-tooltip bottom v-if="enableRefund(item)">
          <template v-slot:activator="{ on }">
            <v-btn @click="refundDialog(item)" icon v-on="on" :loading="false">
              <v-icon v-if="item.payment_type_name === 'cash'">
                mdi-cash-refund
              </v-icon>
              <v-icon v-else>mdi-credit-card-refund</v-icon>
            </v-btn>
          </template>
          <span>Rollback transaction</span>
        </v-tooltip>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import { mapActions, mapState, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  mounted() {
    EventBus.$on("payment-history-refund", event => {
      if (event.payload && this.selected_payment) {
        this.refund();
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("payment-history-refund");
  },

  data() {
    return {
      selected_payment: null,
      headers: [
        {
          text: "#",
          value: "id",
          sortable: false
        },
        {
          text: "Operator",
          value: "created_by_name",
          sortable: false
        },
        {
          text: "Date",
          value: "created_at",
          sortable: false
        },
        {
          text: "Type",
          value: "payment_type_name",
          sortable: false
        },
        {
          text: "Amount",
          value: "price",
          sortable: false
        },
        {
          text: "Change",
          value: "change_price",
          sortable: false
        },
        {
          text: "Status",
          value: "status",
          sortable: false
        },
        {
          text: "Actions",
          value: "actions",
          sortable: false
        }
      ]
    };
  },

  computed: {
    ...mapState("cart", ["payments"])
  },

  methods: {
    ...mapMutations("cart", [
      "setPaymentRefundedStatus",
      "setPayments",
      "setOrderChangePrice",
      "setOrderRemainingPrice",
      "setOrderStatus"
    ]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("requests", ["request"]),

    changePrice(change_price) {
      if (change_price) {
        return this.parsePrice(change_price);
      } else {
        return this.$price();
      }
    },
    changePriceColor(change_price) {
      if (!this.changePrice.isZero() && this.changePrice.isPositive()) {
        return "amber--text";
      } else {
        return null;
      }
    },
    enableRefund(item) {
      if (item.status === "approved" && item.is_refundable) {
        return true;
      } else {
        return false;
      }
    },
    parseStatus(status) {
      return _.upperFirst(status);
    },
    statusColor(status) {
      switch (status) {
        case "approved":
          return "green--text";
        case "failed":
          return "red--text";
        default:
          return null;
      }
    },
    refund() {
      const payload = {
        method: "delete",
        url: `payments/${this.selected_payment.id}`
      };

      this.request(payload)
        .then(response => {
          if (response.refunded_payment_id) {
            const index = _.findIndex(this.payments, {
              id: response.refunded_payment_id
            });

            this.setPaymentRefundedStatus(index);
            this.setPayments(response.refund);
          }

          this.setOrderChangePrice(response.change);
          this.setOrderRemainingPrice(response.remaining);
          this.setOrderStatus(response.order_status);
        })
        .catch(error => {
          console.log(error);
          // @TODO fix payload object
          if (_.has(error, "response.data.refund")) {
            this.payments = error.response.data.refund;
            this.$emit("refund", error.response.data);
          } else {
            console.error(error);
          }
        });
    },
    refundDialog(item) {
      this.selected_payment = item;
      const payload = {
        show: true,
        width: 600,
        title: `Verify your password to rollback payment #${item.id}`,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: "payment-history-refund"
      };

      this.setDialog(payload);
    }
  }
};
</script>
