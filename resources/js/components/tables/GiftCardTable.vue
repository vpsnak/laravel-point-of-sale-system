<template>
  <data-table v-if="render">
    <template v-slot:item.price="{ item }">
      <h4>{{ parsePrice(item.price).toFormat() }}</h4>
    </template>

    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            @click="rechargeGiftcardDialog(item)"
            :disabled="data_table.loading"
            class="mr-4"
            v-on="on"
          >
            <v-icon>mdi-credit-card-plus</v-icon>
          </v-btn>
        </template>
        <span>Recharge</span>
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            :disabled="data_table.loading"
            @click.stop="(item.form = form), editItem(item)"
            class="my-4"
            v-on="on"
          >
            <v-icon>edit</v-icon>
          </v-btn>
        </template>
        <span>Edit</span>
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
        <span>View</span>
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

    this.setDataTable({
      icon: "mdi-wallet-giftcard",
      title: "Gift Cards",
      model: "giftcards",
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
      const payload = {
        show: true,
        width: 400,
        title: `Recharge giftcard: ${item.code}`,
        titleCloseBtn: true,
        icon: "mdi-wallet-giftcard",
        component: "RechargeGiftCardToCart",
        model: item,
        persistent: true,
        eventChannel: "gift-card-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
