<template>
  <data-table v-if="render">
    <template v-slot:item.customer="{ item }">
      <customerChip menu :customer="item.customer" :small="smallChips" />
    </template>
    <template v-slot:item.store="{ item }">
      <storeChip menu :store="item.store" :small="smallChips" />
    </template>
    <template v-slot:item.method="{ item }">
      <orderMethodChip :method="item.method" :small="smallChips" />
    </template>
    <template v-slot:item.status="{ item }">
      <orderStatusChip
        :latestStatus="item.status"
        :orderId="Number(item.id)"
        menu
        :small="smallChips"
      />
    </template>
    <template v-slot:item.total_price="{ item }">
      <orderTotalChip
        menu
        :totalPrice="item.total_price"
        :mdsePrice="item.mdse_price"
        :taxPrice="item.tax_price"
        :deliveryFeesPrice="item.delivery_fees_price"
        :small="smallChips"
      />
    </template>
    <template v-slot:item.income_price="{ item }">
      <orderTotalPaidChip
        :orderId="item.id"
        menu
        :income_price="item.income_price"
        :small="smallChips"
      />
    </template>
    <template v-slot:item.created_by="{ item }">
      <userChip menu :user="item.created_by" :small="smallChips" />
    </template>

    <template v-slot:item.actions="{ item }">
      <v-menu :nudge-width="200" offset-y dark>
        <template v-slot:activator="{ on }">
          <v-btn :disabled="data_table.loading" icon v-on="on">
            <v-icon v-text="'mdi-dots-vertical'" />
          </v-btn>
        </template>
        <v-list dense>
          <v-subheader v-html="`<b>Order #${item.id}</b>`" />
          <v-list-item
            :to="{ name: 'viewOrderDetails', params: { id: item.id } }"
          >
            <v-list-item-icon>
              <v-icon v-text="'mdi-eye'" />
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="'View'" />
            </v-list-item-content>
          </v-list-item>

          <v-list-item @click="reorder(item.id)">
            <v-list-item-icon>
              <v-icon v-text="'mdi-cart-arrow-down'" />
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="'Reorder'" />
            </v-list-item-content>
          </v-list-item>

          <v-divider />

          <v-list-item @click="printOrder(item.id)">
            <v-list-item-icon>
              <v-icon v-text="'mdi-printer'" />
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="'Print'" />
            </v-list-item-content>
          </v-list-item>

          <v-divider />

          <v-list-item
            :disabled="!canReceipt(item)"
            @click="printCustomerOrder(item.id)"
          >
            <v-list-item-icon>
              <v-icon v-text="'mdi-receipt'" />
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="'Print receipt'" />
            </v-list-item-content>
          </v-list-item>

          <v-list-item
            :disabled="!canCheckout(item.status)"
            @click="checkout(item.id)"
          >
            <v-list-item-icon>
              <v-icon v-text="'mdi-currency-usd'" />
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="'Continue checkout'" />
            </v-list-item-content>
          </v-list-item>

          <v-divider />

          <v-list-item
            :disabled="!canCancel(item.status)"
            @click="cancelOrderDialog(item)"
          >
            <v-list-item-icon>
              <v-icon v-text="'mdi-cancel'" />
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="'Cancel order'" />
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-menu>
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
        this.cancelOrder()
          .then(() => {
            EventBus.$emit("data-table", { payload: { action: "paginate" } });
          })
          .finally(() => {
            this.setLoading(false);
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
      smallChips: true,
      viewForm: "order",
      selectedItem: null,
      table: {
        icon: "mdi-file-multiple",
        title: "Orders",
        model: "orders",
        searchField: false,
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
    ...mapMutations("dialog", ["setDialog"]),
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
    printCustomerOrder(id) {
      window.open(`/customer-order/${id}`, "_blank");
    },
    printOrder(id) {
      window.open(`/order/${id}`, "_blank");
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
          this.$router.push({ name: "sale" });
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
          const payload = {
            show: true,
            width: 600,
            title: `Receipt #${id}`,
            titleCloseBtn: true,
            icon: "mdi-receipt",
            component: "orderReceipt",
            persistent: true,
            eventChannel: "orders-table-receipt"
          };
          this.setDialog(payload);
        })
        .catch(error => {
          console.error(error);
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
        const payload = {
          method: "get",
          url: `orders/get/${id}`
        };
        this.request(payload).then(response => {
          this.resetState();
          this.loadOrder(response);
          resolve(true);
        });
      });
    },
    cancelOrderDialog(item) {
      this.selectedItem = item;
      const payload = {
        show: true,
        width: 600,
        title: `Verify your password to cancel order #${item.id}`,
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        component_props: { action: "verify" },
        persistent: true,
        eventChannel: "order-table-cancel-order"
      };

      this.setDialog(payload);
    },
    cancelOrder() {
      return new Promise((resolve, reject) => {
        const payload = {
          method: "get",
          url: `orders/${this.selectedItem.id}`
        };

        this.request(payload)
          .then(() => {
            resolve(true);
          })
          .catch(error => {
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
