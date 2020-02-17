<template>
  <v-container fluid class="fill-height">
    <v-row justify="center" align="center" v-if="loading">
      <div class="text-center ma-12">
        <v-progress-circular
          :indeterminate="true"
          :size="100"
          :width="10"
          color="light-blue"
        ></v-progress-circular>
      </div>
    </v-row>

    <v-row v-else>
      <v-col cols="12">
        <v-card elevation="12">
          <v-tabs grow centered v-model="selected_tab">
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
              <OrderSummaryTabItem />
              <OrderItemsTabItem />
              <OrderDeliveryOptionsTabItem />
            </v-tabs-items>
          </v-tabs>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
  data() {
    return {
      loading: true,
      selected_tab: null
    };
  },

  created() {
    console.log(this.$route.params);
    this.getOrder(this.$route.params.id).then(() => {
      this.loading = false;
    });
  },

  computed: {
    ...mapState("cart", ["cart_products"])
  },
  methods: {
    ...mapActions(["getOne"]),
    ...mapActions("cart", ["loadOrder"]),

    getOrder(id) {
      return new Promise((resolve, reject) => {
        this.getOne({
          model: "orders",
          data: { id }
        })
          .then(response => {
            this.loadOrder(response);
            resolve(true);
          })
          .catch(error => {
            // unhandled error
            console.error(error);
            reject(error);
          });
      });
    }
  }
};
</script>
