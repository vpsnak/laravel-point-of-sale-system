<template>
  <div>
    <data-table v-if="render">
      <template v-slot:item.customer="{ item }">
        {{ item.customer ? item.customer.email : "Guest" }}
      </template>
      <template v-slot:item.total="{ item }"> $ {{ item.total }} </template>
      <template v-slot:item.total_paid="{ item }">
        $ {{ item.total_paid }}
      </template>

      <template v-slot:item.status="{ item }">
        <span :class="statusColor(item.status)">
          <b>{{ parseStatusName(item.status) }}</b>
        </span>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-tooltip bottom v-if="['paid', 'complete'].indexOf(item.status) >= 0">
          <template v-slot:activator="{ on }">
            <v-btn
              :ref="item.id"
              small
              :disabled="data_table.loading"
              @click.stop="receipt(item.id)"
              class="my-2"
              icon
              v-on="on"
            >
              <v-icon small>mdi-receipt</v-icon>
            </v-btn>
          </template>
          <span>Receipt</span>
        </v-tooltip>

        <v-tooltip
          v-if="['pending', 'pending_payment'].indexOf(item.status) >= 0"
          bottom
        >
          <template v-slot:activator="{ on }">
            <v-btn
              :ref="item.id"
              small
              :disabled="data_table.loading"
              @click.stop="checkout(item.id)"
              class="my-2"
              icon
              v-on="on"
            >
              <v-icon small>mdi-currency-usd</v-icon>
            </v-btn>
          </template>
          <span>Continue checkout</span>
        </v-tooltip>

        <v-tooltip bottom v-if="item.status !== 'canceled'" color="red">
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              small
              color="red"
              :disabled="data_table.loading"
              @click.stop="cancelOrderDialog(item)"
              class="my-2"
              v-on="on"
            >
              <v-icon small>mdi-cancel</v-icon>
            </v-btn>
          </template>
          <span>Cancel order</span>
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              :to="{ name: 'editOrder', params: { id: item.id } }"
              small
              :disabled="data_table.loading"
              class="my-2"
              v-on="on"
              icon
            >
              <v-icon small>edit</v-icon>
            </v-btn>
          </template>
          <span>Edit</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              small
              :disabled="data_table.loading"
              :to="{ name: 'viewOrder', params: { id: item.id } }"
              class="my-2"
              v-on="on"
              icon
            >
              <v-icon small>mdi-eye</v-icon>
            </v-btn>
          </template>
          <span>View</span>
        </v-tooltip>
      </template>
    </data-table>
  </div>
</template>

<script>
import { EventBus } from "../../plugins/event-bus";
import { mapMutations, mapActions, mapState } from "vuex";

export default {
  mounted() {
    this.render = false;
    this.resetDataTable();

    this.setDataTable({
      icon: "mdi-buffer",
      title: "Orders",
      model: "orders",
      disableNewBtn: true,
      loading: true
    });

    EventBus.$on("order-table-cancel-order", event => {
      if (event.payload && this.selectedItem) {
        this.setLoading(true);
        this.cancelOrder().then(() => {
          EventBus.$emit("data-table", { action: "paginate" });
        });
      }
    });

    this.render = true;
  },

  beforeDestroy() {
    EventBus.$off();
  },

  data() {
    return {
      render: false,
      viewForm: "order",
      selectedItem: null
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapActions(["getOne", "delete"]),
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
    ...mapMutations("datatable", [
      "setLoading",
      "setDataTable",
      "resetDataTable"
    ]),
    ...mapMutations("cart", ["setCheckoutDialog", "resetState"]),
    ...mapActions("cart", ["loadOrder"]),

    parseStatusName(value) {
      if (value) {
        return _.upperFirst(value.replace("_", " "));
      }
    },
    statusColor(status) {
      switch (status) {
        case "canceled":
          return "red--text";
        case "pending":
          return "amber--text";
        case "pending_payment":
          return "amber--text";
        case "paid":
          return "cyan--text";
        case "complete":
          return "green--text";
        default:
          return "";
      }
    },
    receipt(id) {
      this.setLoading(true);

      this.getSingleOrder(id)
        .then(() => {
          // this.setDialog({});
        })
        .catch()
        .finally(() => {
          this.setLoading(false);
        });
    },
    checkout(id) {
      this.setLoading(true);

      this.getSingleOrder(id)
        .then(() => {
          this.setCheckoutDialog(true);
        })
        .catch()
        .finally(() => {
          this.setLoading(false);
        });
    },
    getSingleOrder(id) {
      return new Promise((resolve, reject) => {
        this.getOne({
          model: "orders",
          data: { id }
        })
          .then(response => {
            this.resetState();
            this.loadOrder(response);
            resolve(true);
          })
          .catch(error => {
            // unhandled error
            console.error(error);
            reject(error);
          });
      });
    },
    cancelOrderDialog(item) {
      this.selectedItem = item;

      this.setDialog({
        show: true,
        width: 600,
        title: `Verify your password to cancel order #${item.id}`,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: "order-table-cancel-order"
      });
    },
    cancelOrder() {
      return new Promise((resolve, reject) => {
        let payload = {
          model: "orders",
          id: this.selectedItem.id
        };

        this.delete(payload)
          .then(() => {
            resolve(true);
          })
          .catch(error => {
            // unhandled error
            console.error(error);
            reject(error);
          })
          .finally(() => {
            this.selectedItem = null;
          });
      });
    }
  }
};
</script>
