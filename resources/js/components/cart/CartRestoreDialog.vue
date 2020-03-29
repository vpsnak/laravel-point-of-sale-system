<template>
  <v-container fluid>
    <v-data-iterator
      v-if="!cartsLoading"
      v-model="selected"
      :items="carts"
      item-key="id"
      :items-per-page="4"
      hide-default-footer
      no-data-text="No carts found"
      single-select
      show-select
    >
      <template v-slot:default="{ items, select }">
        <v-row>
          <v-col
            v-for="(item, index) in items"
            :key="item.id"
            :cols="12"
            :sm="6"
            :md="4"
          >
            <v-card ripple @click="select = true">
              <v-card-title>
                <h6 v-text="`${1 + index}. ${item.created_at}`" />
              </v-card-title>
              <v-divider />
              <v-list>
                <v-list-item>
                  <v-list-item-content v-text="'Items:'" />
                  <v-list-item-content
                    class="align-end"
                    v-text="item.item_count"
                  />
                </v-list-item>
                <v-list-item>
                  <v-list-item-content v-text="'Method:'" />
                  <v-list-item-content class="align-end" v-text="item.method" />
                </v-list-item>
                <v-list-item>
                  <v-list-item-content v-text="'Customer:'" />
                  <v-list-item-content
                    class="align-end"
                    v-text="item.customer"
                  />
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </template>
      <template v-slot:footer>
        <v-row class="mt-2" align="center" justify="end">
          <v-pagination
            v-model="currentPage"
            :length="lastPage"
            @input="getCarts()"
            :total-visible="5"
          />
        </v-row>
      </template>
    </v-data-iterator>

    <v-row v-else>
      <v-col v-for="i in 3" :key="i" :cols="12" :sm="6" :md="4">
        <v-skeleton-loader type="card-heading" tile class="mx-auto" />
        <v-divider />
        <v-skeleton-loader type="image" tile class="mx-auto" />
      </v-col>
      <v-col :cols="12">
        <v-skeleton-loader
          type="heading"
          tile
          class="mx-auto d-flex justify-center"
          max-width="600px"
          width="100%"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
  props: {
    show: Boolean
  },

  data() {
    return {
      cartsLoading: false,
      carts: [],
      currentPage: 1,
      lastPage: null,
      cartReplacePrompt: false,
      selected: []
    };
  },
  watch: {
    selected(value) {
      console.log(value);
    }
  },
  mounted() {
    this.getCarts();
  },

  computed: {
    ...mapState("cart", ["cart_products"])
  },

  methods: {
    ...mapActions("requests", ["request"]),

    confirmation(event) {
      if (event) {
        this.loadCart();
      }

      this.cartReplacePrompt = false;
    },
    getCarts() {
      this.cartsLoading = true;
      const payload = {
        method: "get",
        url: `carts?page=${this.currentPage}`
      };
      this.request(payload).then(response => {
        this.currentPage = response.current_page;
        this.lastPage = response.last_page;
        this.carts = response.data;

        this.cartsLoading = false;
      });
    },
    restoreCart() {
      if (_.size(this.cart_products)) {
        this.cartReplacePrompt = true;
      } else {
        this.loadCart();
      }
    },
    loadCart() {
      const cart = JSON.parse(this.selectedCart.cart).products;
      this.$store.state.cart.products = cart.products;
      this.$store.state.cart.shipping.notes = cart.shipping.notes;
      this.$store.state.cart.discount_type = cart.discount_type;
      this.$store.state.cart.discount_amount = cart.discount_amount;

      this.removeCart();
      this.close();
    },
    removeCart() {
      const payload = {
        method: "delete",
        url: `carts/${this.selectedCart.id}`
      };

      this.request(payload).then(() => {
        this.carts = this.carts.filter(cart => {
          return cart.id !== this.selectedCart.id;
        });
      });
    }
  }
};
</script>
