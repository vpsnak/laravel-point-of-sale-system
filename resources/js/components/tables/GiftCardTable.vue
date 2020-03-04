<template>
  <div>
    <data-table v-if="render">
      <template v-slot:item.enabled="{ item }">
        {{ item.enabled ? "Yes" : "No" }}
      </template>

      <template v-slot:item.actions="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              @click="rechargeGiftcardDialog(item)"
              :disabled="data_table.loading"
              class="my-2"
              icon
              v-on="on"
            >
              <v-icon small>mdi-credit-card-plus</v-icon>
            </v-btn>
          </template>
          <span>Recharge</span>
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              small
              :disabled="data_table.loading"
              @click.stop="(item.form = form), editItem(item)"
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
              @click.stop="(item.form = form), viewItem(item)"
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
import { mapMutations, mapState } from "vuex";
import { EventBus } from "../../plugins/eventBus";

export default {
  mounted() {
    this.resetDataTable();

    this.setDataTable({
      icon: "mdi-wallet-giftcard",
      title: "Gift Cards",
      model: "gift-cards",
      newForm: this.form,
      btnTxt: "New Gift Card",
      loading: true
    });

    EventBus.$on("gift-card-table", event => {
      if (event.payload) {
        this.setCheckoutDialog(true);
      }
    });

    this.render = true;
  },

  beforeDestroy() {
    this.$off();
  },

  data() {
    return {
      render: false,
      form: "giftCardForm"
    };
  },

  computed: {
    ...mapState("datatable", ["data_table"])
  },

  methods: {
    ...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
    ...mapMutations("datatable", ["setDataTable", "resetDataTable"]),
    ...mapMutations("cart", ["setCheckoutDialog"]),

    rechargeGiftcardDialog(item) {
      this.setDialog({
        show: true,
        width: 400,
        title: `Recharge the giftcard #${item.id}`,
        titleCloseBtn: true,
        icon: "mdi-wallet-giftcard",
        component: "RechargeGiftCardToCart",
        model: item,
        persistent: true,
        eventChannel: "gift-card-table"
      });
    }
  }
};
</script>
