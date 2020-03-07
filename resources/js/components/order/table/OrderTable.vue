<template>
  <data-table v-if="render">
    <template v-slot:item.customer="{ item }">
      <customerChip :menu="true" :customer="item.customer" />
    </template>
    <template v-slot:item.store="{ item }">
      <storeChip :menu="true" :store="item.store" />
    </template>
    <template v-slot:item.method="{ item }">
      <orderMethodChip :method="item.method" />
    </template>
    <template v-slot:item.status="{ item }">
      <orderStatusChip
        :latestStatus="item.status"
        :orderId="item.id"
        :menu="true"
      />
    </template>
    <template v-slot:item.created_by="{ item }">
      <createdByChip :menu="true" :created_by="item.created_by" />
    </template>
    <template v-slot:item.total="{ item }">
      {{ parsePrice(item.total_price).toFormat() }}
    </template>
    <template v-slot:item.total_paid="{ item }">
      {{ parsePrice(item.paid_price).toFormat() }}
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip v-if="canCheckout(item.status)" bottom>
        <template v-slot:activator="{ on }">
          <v-btn
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

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            small
            :disabled="data_table.loading"
            @click.stop="reorder(item.id)"
            class="my-2"
            icon
            v-on="on"
          >
            <v-icon small>mdi-cart-arrow-down</v-icon>
          </v-btn>
        </template>
        <span>Reorder</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            small
            :disabled="data_table.loading"
            :to="{ name: 'viewOrderDetails', params: { id: item.id } }"
            class="my-2"
            v-on="on"
            icon
          >
            <v-icon small>mdi-eye</v-icon>
          </v-btn>
        </template>
        <span>View</span>
      </v-tooltip>

      <v-tooltip bottom v-if="canReceipt(item.status)">
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

      <v-tooltip bottom v-if="canCancel(item.status)" color="red">
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
    </template>
  </data-table>
</template>

<script>
import { EventBus } from "../../../plugins/eventBus";
import { mapMutations, mapActions, mapState } from "vuex";

export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable(this.table);

    EventBus.$on("order-table-cancel-order", event => {
      if (event.payload && this.selectedItem) {
        this.setLoading(true);
        this.cancelOrder().then(() => {
          EventBus.$emit("data-table", { payload: { action: "paginate" } });
        });
      }
    });

    EventBus.$on("orders-table-receipt", () => {
      this.resetState();
    });

    this.render = true;
  },

  beforeDestroy() {
    EventBus.$off("order-table-cancel-order");
    EventBus.$off("orders-table-receipt");
  },

  data() {
    return {
      render: false,
      viewForm: "order",
      selectedItem: null,
      table: {
        icon: "mdi-buffer",
        title: "Orders",
        model: "orders",
        disableNewBtn: true,
        loading: true,
        filters: "orderTableFilters"
      }
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapMutations("cart", ["setCheckoutDialog", "resetState", "setReorder"]),
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
    ...mapMutations("datatable", [
      "setLoading",
      "setDataTable",
      "resetDataTable"
    ]),
    ...mapActions("requests", ["request"]),
    ...mapActions("cart", ["loadOrder"]),

    canRefund(status) {
      if (status && status.can_refund) {
        return true;
      } else {
        return false;
      }
    },
    canMasReupload(status) {
      if (status && status.can_mas_reupload) {
        return true;
      } else {
        return false;
      }
    },
    canMasUpload(status) {
      if (status && status.can_mas_upload) {
        return true;
      } else {
        return false;
      }
    },
    canReceipt(status) {
      if (status && status.can_receipt) {
        return true;
      } else {
        return false;
      }
    },
    canCheckout(status) {
      if (status && status.can_checkout) {
        return true;
      } else {
        return false;
      }
    },
    canCancel(status) {
      if (status && status.can_cancel) {
        return true;
      } else {
        return false;
      }
    },
    reorder(id) {
      this.setLoading(true);
      this.resetState();
      this.request({
        method: "get",
        url: `orders/get/${id}`
      })
        .then(response => {
          this.setReorder(response.items);
          this.$router.push({ name: "sales" });
          this.setCheckoutDialog(true);
        })
        .finally(() => {
          this.setLoading(false);
        });
    },
    receipt(id) {
      this.setLoading(true);

      this.getSingleOrder(id)
        .then(() => {
          this.setDialog({
            show: true,
            width: 600,
            title: `Receipt #${id}`,
            titleCloseBtn: true,
            icon: "mdi-receipt",
            component: "orderReceipt",
            persistent: true,
            eventChannel: "orders-table-receipt"
          });
        })
        .catch(error => {
          console.log(error);
        })
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
        .finally(() => {
          this.setLoading(false);
        });
    },
    getSingleOrder(id) {
      return new Promise((resolve, reject) => {
        this.request({
          method: "get",
          url: `orders/get/${id}`
        }).then(response => {
          this.resetState();
          this.loadOrder(response);
          resolve(true);
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
        const payload = {
          method: "delete",
          url: `orders${this.selectedItem.id}`
        };

        this.request(payload)
          .then(() => {
            resolve(true);
          })
          .finally(() => {
            this.selectedItem = null;
          });
      });
    }
  }
};
</script>
