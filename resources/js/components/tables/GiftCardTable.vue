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
            class="my-4"
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
            @click.stop="edit(item)"
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
            @click.stop="view(item)"
            class="my-4"
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
      newDialogProps: {
        title: "Add gift card",
        component: "giftCardForm"
      },
      newBtnTxt: "add gift card"
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
    ...mapMutations("dialog", ["setDialog"]),
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
        component_props: { model: item },
        persistent: true,
        eventChannel: "gift-card-table"
      };
      this.setDialog(payload);
    },
    view(item) {
      const payload = {
        show: true,
        width: 400,
        title: `View: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-eye",
        component: "giftCardForm",
        component_props: { model: item },
        readonly: true,
        eventChannel: ""
      };
      this.setDialog(payload);
    },
    edit(item) {
      const payload = {
        show: true,
        width: 400,
        title: `Edit: ${item.name}`,
        titleCloseBtn: true,
        icon: "mdi-pencil",
        component: "giftCardForm",
        component_props: { model: _.cloneDeep(item) },
        persistent: true,
        eventChannel: "data-table"
      };
      this.setDialog(payload);
    }
  }
};
</script>
