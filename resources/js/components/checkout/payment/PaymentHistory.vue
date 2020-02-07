<template>
  <div>
    <v-col :cols="12">
      <h3 class="mb-2">Payment history</h3>
    </v-col>

    <v-col :cols="12">
      <v-data-table
        :headers="headers"
        :items="paymentHistory"
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
    </v-col>
  </div>
</template>

<script>
import { mapActions, mapState, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
  mounted() {
    console.log("asd");
    EventBus.$on("payment-history-refund", event => {
      console.log("asd");
      console.log(event);

      if (event.payload) {
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
      paymentID: null,
      headers: [
        {
          text: "Payment ID",
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

    paymentHistory: {
      get() {
        return this.order.payments;
      },
      set(value) {
        this.$store.commit("cart/setPaymentHistory", value);
      }
    },
    orderId() {
      return this.order.id;
    },
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
      if (this.orderId) {
        this.paymentHistoryLoading = true;

        let payload = {
          model: "payments",
          keyword: this.orderId
        };
        this.search(payload)
          .then(response => {
            this.paymentHistory = response;
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
        id: this.paymentID
      };

      this.delete(payload)
        .then(response => {
          this.$emit("refund", response);
        })
        .catch(error => {
          if (error.response.data.refund) {
            this.payments = error.response.data.refund;
          }

          this.$emit("refund", error.response.data);
        });
    },

    refundDialog(item) {
      const dialog = {
        width: 600,
        title: `Verify your password to refund payment #${item.id}`,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: "payment-history-refund"
      };

      this.paymentID = item.id;

      this.setDialog(dialog);
    }
  }
};
</script>
