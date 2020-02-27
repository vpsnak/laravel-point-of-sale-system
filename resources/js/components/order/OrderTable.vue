<template>
  <div>
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
        <orderStatusChip :status="item.status" />
      </template>
      <template v-slot:item.mas_order="{ item }">
        <masStatusChip :mas_order="item.mas_order" />
      </template>
      <template v-slot:item.created_by="{ item }">
        <createdByChip :menu="true" :created_by="item.created_by" />
      </template>
      <template v-slot:item.total="{ item }"> $ {{ item.total }} </template>
      <template v-slot:item.total_paid="{ item }">
        $ {{ item.total_paid }}
      </template>

      <template v-slot:item.actions="{ item }">
        <v-tooltip
          v-if="['pending', 'pending_payment'].indexOf(item.status) >= 0"
          bottom
        >
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

        <v-tooltip bottom v-if="disableIfStatus(item)" color="red">
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
      selectedItem: null
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

    disableIfStatus(item) {
      switch (item.status) {
        case "completed":
        case "canceled":
          return false;
        default:
          return true;
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
