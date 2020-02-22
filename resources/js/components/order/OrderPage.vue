<template>
  <v-container fluid :class="loading ? 'fill-height' : ''">
    <v-row justify="center" align="center" v-if="loading || !order_id">
      <div class="text-center ma-12">
        <v-progress-circular
          :indeterminate="true"
          :size="100"
          :width="10"
          color="light-blue"
        ></v-progress-circular>
      </div>
    </v-row>

    <v-row no-gutters v-else-if="order_id">
      <v-col cols="12">
        <v-card class="fill-height">
          <v-card-title>
            <v-icon class="pr-2">
              edit
            </v-icon>

            Edit order # {{ order_id }}
            <v-spacer />

            <v-tooltip bottom color="red">
              <template v-slot:activator="{ on }">
                <v-btn @click.stop="close()" color="red" icon v-on="on">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>Close</span>
            </v-tooltip>
          </v-card-title>

          <v-tabs grow centered show-arrows v-model="selected_tab">
            <v-tab>
              <v-icon left>mdi-clipboard-list-outline</v-icon>
              <span>Order summary & actions</span>
            </v-tab>
            <v-tab>
              <v-icon left>mdi-package-variant</v-icon>
              <span>
                Items <b class="primary--text">({{ cart_products.length }})</b>
              </span>
            </v-tab>
            <v-tab>
              <v-icon left>mdi-file-document-edit-outline</v-icon>
              <span>Order Options</span>
            </v-tab>

            <v-tabs-items v-model="selected_tab">
              <OrderSummaryTabItem :editable="this.$props.editable" />
              <OrderItemsTabItem :editable="this.$props.editable" />
              <OrderDeliveryOptionsTabItem />
            </v-tabs-items>
          </v-tabs>
          <v-tooltip bottom v-if="order_id && $props.editable" color="primary">
            <template v-slot:activator="{ on }">
              <v-btn
                color="primary"
                absolute
                bottom
                right
                fab
                v-on="on"
                @click.stop="setOrderPageActions(true)"
              >
                <v-icon>mdi-cursor-default-click-outline</v-icon>
              </v-btn>
            </template>
            <span>Actions</span>
          </v-tooltip>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
export default {
  props: {
    editable: Boolean
  },
  data() {
    return {
      loading: true,
      selected_tab: null
    };
  },

  created() {
    this.getOrder(this.$route.params.id).then(() => {
      this.loading = false;
    });
  },

  computed: {
    ...mapState("cart", ["cart_products", "order_id", "order_page_actions"])
  },
  methods: {
    ...mapMutations("cart", ["resetState", "setOrderPageActions"]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("cart", ["loadOrder"]),
    ...mapActions("requests", ["request"]),

    confirmationDialog() {
      this.setDialog({
        show: true,
        action: "confirmation",
        title: "Cancel order?",
        content: "Are you sure you want to <b>cancel</b> the current order?",
        action: "confirmation",
        persistent: true,
        cancelBtnTxt: "No",
        confirmationBtnTxt: "Yes"
      });
    },
    close() {
      this.$router.go(-1);
      this.resetState();
    },
    getOrder(id) {
      return new Promise((resolve, reject) => {
        this.request({
          method: "get",
          url: `orders/get/${id}`
        })
          .then(response => {
            this.loadOrder(response);
            resolve(true);
          })
          .catch(error => {
            reject(error);
          });
      });
    }
  }
};
</script>
