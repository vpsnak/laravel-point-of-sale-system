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
      :items="transactions"
      class="elevation-1"
      disable-pagination
      disable-filtering
      hide-default-footer
      :loading="false"
    >
      <template v-slot:item.price="{ item }">
        <b :class="statusColor(item.status, 'amount')">
          {{ parsePrice(item.price).toFormat() }}
        </b>
      </template>

      <template v-slot:item.earnings_price="{ item }">
        <b :class="statusColor(item.status, 'earnings')">
          {{ earningsPrice(item) }}
        </b>
      </template>

      <template v-slot:item.change_price="{ item }">
        <b :class="changePriceColor(item)" v-if="!changePrice(item).isZero()">
          {{ changePrice(item).toFormat() }}
        </b>
      </template>

      <template v-slot:item.status="{ item }">
        <v-chip class="ma-2" :color="statusColor(item.status, 'status')">
          <v-avatar left>
            <v-icon>{{ parseStatusIcon(item.status) }}</v-icon>
          </v-avatar>
          <b>
            {{ parseStatus(item.status) }}
          </b>
        </v-chip>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-tooltip bottom v-if="enableRefund(item)">
          <template v-slot:activator="{ on }">
            <v-btn
              @click="refundDialog(item)"
              icon
              v-on="on"
              :loading="rollbackLoading"
            >
              <v-icon>
                mdi-undo
              </v-icon>
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
    EventBus.$on("transaction-history-refund", event => {
      if (event.payload && this.selected_payment) {
        this.rollbackPayment();
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("transaction-history-refund");
  },

  data() {
    return {
      rollbackLoading: false,
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
          value: "type_name",
          sortable: false
        },
        {
          text: "Amount",
          value: "price",
          sortable: false
        },
        {
          text: "Earnings",
          value: "earnings_price",
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
    ...mapState("cart", ["transactions"])
  },

  methods: {
    ...mapMutations("cart", [
      "setPaymentRefundedStatus",
      "setTransactions",
      "setOrderChangePrice",
      "setOrderRemainingPrice",
      "setOrderStatus",
      "setCheckoutLoading"
    ]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("requests", ["request"]),

    earningsPrice(transaction) {
      if (_.isObjectLike(transaction.payment)) {
        if (transaction.status === "failed") {
          return this.parsePrice().toFormat();
        } else {
          return this.parsePrice(transaction.price)
            .subtract(this.changePrice(transaction))
            .toFormat();
        }
      } else {
        return this.parsePrice(transaction.price)
          .subtract(this.changePrice(transaction))
          .multiply(-1)
          .toFormat();
      }
    },
    changePrice(transaction) {
      if (
        _.has(transaction.payment, "change_price") &&
        this.parsePrice(transaction.payment.change_price).isPositive()
      ) {
        return this.parsePrice(transaction.payment.change_price);
      } else {
        return this.parsePrice();
      }
    },
    changePriceColor(item) {
      if (
        !this.changePrice(item).isZero() &&
        this.changePrice(item).isPositive()
      ) {
        return "amber--text";
      } else {
        return null;
      }
    },
    enableRefund(item) {
      if (
        _.has(item.payment, "refundable_price") &&
        this.parsePrice(item.payment.refundable_price).greaterThan(
          this.parsePrice()
        )
      ) {
        return true;
      } else {
        return false;
      }
    },
    parseStatus(status) {
      return _.upperFirst(status);
    },
    parseStatusIcon(status) {
      if (this.statusColor(status, "status") === "green") {
        return "mdi-check";
      } else {
        return "mdi-alert-circle-outline";
      }
    },
    statusColor(status, column) {
      switch (status) {
        case "approved":
          switch (column) {
            case "status":
              return "green";
            case "amount":
              return "primary--text";
            case "earnings":
              return "green--text";
          }
        case "failed":
          switch (column) {
            case "status":
              return "red";
            case "amount":
              return "primary--text";
            case "earnings":
              return "amber--text";
          }
        case "refund approved":
          switch (column) {
            case "status":
              return "green";
            case "amount":
              return "primary--text";
            case "earnings":
              return "red--text";
          }
        default:
          return null;
      }
    },
    rollbackPayment() {
      this.setCheckoutLoading(true);
      this.rollbackLoading = true;
      const payload = {
        method: "post",
        url: `transactions/${this.selected_payment.id}/rollback`
      };

      this.request(payload)
        .then(response => {
          if (response.refunded_payment_id) {
            const index = _.findIndex(this.transactions, {
              id: response.refunded_payment_id
            });

            this.setPaymentRefundedStatus(index);
            this.setTransactions(response.transaction);
          }

          this.setOrderChangePrice(response.change);
          this.setOrderRemainingPrice(response.remaining);
          this.setOrderStatus(response.order_status);
        })
        .catch(error => {
          console.log(error);
          // @TODO fix payload object
          if (_.has(error, "response.transaction")) {
            this.transactions = error.response.data.refund;
          } else {
            console.error(error);
          }
        })
        .finally(() => {
          this.setCheckoutLoading(false);
          this.rollbackLoading = false;
        });
    },
    refundDialog(item) {
      this.selected_payment = item.payment;
      const payload = {
        show: true,
        width: 600,
        title: `Verify your password to rollback payment #${item.id}`,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: "transaction-history-refund"
      };

      this.setDialog(payload);
    }
  }
};
</script>
