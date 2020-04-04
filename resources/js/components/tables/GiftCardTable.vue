<template>
  <data-table v-if="render">
    <template v-slot:item.price="{ item }">
      <h4 v-text="parsePrice(item.price).toFormat()" />
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip v-if="item.enabled_at" bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            @click="rechargeGiftcardDialog(item)"
            :disabled="data_table.loading"
            class="mr-4"
            v-on="on"
          >
            <v-icon v-text="'mdi-credit-card-plus'" />
          </v-btn>
        </template>
        <span v-text="'Recharge'" />
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            :disabled="data_table.loading"
            @click.stop="(item.form = 'giftCardForm'), editItem(item)"
            class="my-4"
            v-on="on"
          >
            <v-icon v-text="'edit'" />
          </v-btn>
        </template>
        <span v-text="'Edit'" />
      </v-tooltip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            :disabled="data_table.loading"
            @click.stop="(item.form = form), viewItem(item)"
            class="ml-4"
            v-on="on"
          >
            <v-icon v-text="'mdi-eye'" />
          </v-btn>
        </template>
        <span v-text="'View'" />
      </v-tooltip>
    </template>
  </data-table>
</template>

<script>
import { mapMutations, mapState } from "vuex";
import { EventBus } from "../../plugins/eventBus";

export default {
  mounted() {
    this.resetDataTable();
    const payload = {
      icon: "mdi-wallet-giftcard",
      title: "Gift Cards",
      model: "giftcards",
      newForm: "giftCardForm",
      btnTxt: "New Gift Card"
    };
    this.setDataTable(payload);

    EventBus.$on("gift-card-table", event => {
      if (event.payload) {
        this.setCheckoutDialog(true);
      }
    });

    this.render = true;
  },

  beforeDestroy() {
    EventBus.$off("gift-card-table");
  },

  data() {
    return {
      render: false
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
      const payload = {
        show: true,
        width: 600,
        title: `Recharge giftcard: ${item.code}`,
        titleCloseBtn: true,
        icon: "mdi-wallet-giftcard",
        component: "giftCardToCartForm",
        model: item,
        persistent: true,
        eventChannel: "gift-card-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
