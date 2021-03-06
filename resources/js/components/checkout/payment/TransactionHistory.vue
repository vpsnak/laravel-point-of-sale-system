<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left v-text="'mdi-currency-usd'" />
      <span class="subheading" v-text="'Transaction history'" />
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
    >
      <template v-slot:item.price="{ item }">
        <b
          :class="statusColor(item.status, 'amount')"
          v-text="parsePrice(item.price).toFormat()"
        />
      </template>

      <template v-slot:item.earnings_price="{ item }">
        <b
          :class="statusColor(item.status, 'earnings')"
          v-text="earningsPrice(item).toFormat()"
        />
      </template>

      <template v-slot:item.change_price="{ item }">
        <b
          v-if="!changePrice(item).isZero()"
          :class="changePriceColor(item)"
          v-text="changePrice(item).toFormat()"
        />
      </template>

      <template v-slot:item.status="{ item }">
        <v-chip small label dark :color="statusColor(item.status, 'status')">
          <v-icon left v-text="parseStatusIcon(item.status)" />
          <b v-text="parseStatus(item.status)" />
        </v-chip>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-tooltip bottom v-if="enableRefund(item) && $props.checkout">
          <template v-slot:activator="{ on }">
            <v-btn
              @click="refundDialog(item)"
              icon
              v-on="on"
              :loading="rollbackLoading"
              :disabled="loading"
            >
              <v-icon v-text="'mdi-undo'" />
            </v-btn>
          </template>
          <span v-text="'Rollback transaction'" />
        </v-tooltip>
        <v-menu
          v-else-if="enableRefund(item)"
          v-model="refundMenu[item.id]"
          :key="item.id"
          :close-on-content-click="false"
          offset-y
        >
          <template v-slot:activator="{ on: menu }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on: tooltip }">
                <v-btn icon v-on="{ ...tooltip, ...menu }">
                  <v-icon v-text="'mdi-cash-refund'" />
                </v-btn>
              </template>
              <span v-text="'Issue a refund'" />
            </v-tooltip>
          </template>
          <orderRefundForm
            :transaction="item"
            @submit="refundSubmit(item.id)"
          />
        </v-menu>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import { mapActions, mapState, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    checkout: Boolean
  },

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
      refundMenu: [],
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
    ...mapState("cart", ["transactions"]),

    loading() {
      if (this.rollbackLoading || this.checkoutLoading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapMutations("cart", [
      "setTransactions",
      "setOrderChangePrice",
      "setOrderRemainingPrice",
      "setOrderStatus",
      "setCheckoutLoading"
    ]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("requests", ["request"]),

    refundSubmit(id) {
      this.refundMenu[id] = false;
    },
    earningsPrice(transaction) {
      if (
        transaction.status === "failed" ||
        transaction.status === "refund failed"
      ) {
        return this.parsePrice();
      } else if (_.has(transaction.payment, "earnings_price")) {
        return this.parsePrice(transaction.payment.earnings_price);
      } else if (_.has(transaction.refund, "id")) {
        return this.parsePrice(transaction.price).multiply(-1);
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
        url: `payments/${this.selected_payment.id}/rollback`
      };

      this.request(payload)
        .then(response => {
          if (response.refunded_transaction) {
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
        component_props: { action: "verify" },
        persistent: true,
        eventChannel: "transaction-history-refund"
      };
      this.setDialog(payload);
    }
  }
};
</script>
