<template>
  <v-card>
    <v-card-title>
      <span class="subheading">History</span>
    </v-card-title>
    <v-card-text>
      <v-data-table
        no-data-text="No payments have been made"
        dense
        height="15vh"
        :headers="headers"
        :items="order.payments"
        class="elevation-1"
        disable-pagination
        disable-filtering
        hide-default-footer
        :loading="loading || refundLoading || paymentHistoryLoading"
      >
        <template v-slot:item.status="{ item }">
          <span :class="statusColor(item.status)">
            <b>{{ parseStatus(item.status) }}</b>
          </span>
        </template>

        <template v-slot:item.actions="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn
                @click="refundDialog(item)"
                icon
                v-on="on"
                :loading="loading || refundLoading || paymentHistoryLoading"
                v-if="item.status === 'approved' && !item.refunded"
              >
                <v-icon v-if="item.payment_type.type === 'cash'"
                  >mdi-cash-refund</v-icon
                >
                <v-icon v-else>mdi-credit-card-refund</v-icon>
              </v-btn>
            </template>
            <span>Refund</span>
          </v-tooltip>
        </template>
      </v-data-table>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapState, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
  mounted() {
    EventBus.$on("payment-history-refund", event => {
      console.log(event);
      if (event.payload && this.selected_payment) {
        this.refund();
      }
    });
  },

  beforeDestroy() {
    this.$off("refund");
    EventBus.$off();
  },

  props: {
    loading: Boolean
  },

  data() {
    return {
      paymentHistoryLoading: false,
      selected_payment: null,
      headers: [
        {
          text: "#",
          value: "id",
          sortable: false
        },
        {
          text: "Operator",
          value: "created_by.name",
          sortable: false
        },
        {
          text: "Date",
          value: "created_at",
          sortable: false
        },
        {
          text: "Type",
          value: "payment_type.name",
          sortable: false
        },
        {
          text: "Status",
          value: "status",
          sortable: false
        },
        {
          text: "Amount (USD)",
          value: "amount",
          sortable: false
        },
        {
          text: "Actions",
          value: "actions"
        }
      ]
    };
  },
  computed: {
    ...mapState("cart", ["order"]),

    refundLoading: {
      get() {
        return this.$store.state.cart.refundLoading;
      },
      set(value) {
        this.$store.state.cart.refundLoading = value;
      }
    }
  },
  methods: {
    ...mapActions(["search", "delete"]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("cart", ["setPaymentHistory"]),

    parseStatus(status) {
      return _.upperFirst(status);
    },
    statusColor(status) {
      switch (status) {
        case "approved":
          return "green--text";
        case "failed":
          return "red--text";
        case "refunded":
          return "orange--text";
        default:
          return null;
      }
    },
    getPaymentHistory() {
      if (this.order.id) {
        this.paymentHistoryLoading = true;

        let payload = {
          model: "payments",
          keyword: this.order.id
        };
        this.search(payload)
          .then(response => {
            this.setPaymentHistory(response);
          })
          .finally(() => {
            this.paymentHistoryLoading = false;
          });
      }
    },
    refund() {
      this.refundLoading = true;
      let payload = {
        model: "payments",
        id: this.selected_payment.id
      };

      this.delete(payload)
        .then(response => {
          this.$emit("refund", response);
        })
        .catch(error => {
          if (error.response.data.refund) {
            this.order.payments = error.response.data.refund;
          }

          this.$emit("refund", error.response.data);
        });
    },

    refundDialog(item) {
      this.selected_payment = item;

      this.setDialog({
        width: 600,
        title: `Verify your password to rollback payment #${item.id}`,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: "payment-history-refund"
      });
    }
  }
};
</script>
