<template>
  <v-container fluid>
    <v-data-iterator
      v-if="!cartsLoading"
      :items="carts"
      :items-per-page="4"
      hide-default-footer
      no-data-text="No carts found"
    >
      <template v-slot:default="{ items }">
        <v-row>
          <v-col
            :cols="12"
            :sm="6"
            :md="4"
            v-for="(item, index) in items"
            :key="item.id"
          >
            <v-card>
              <v-card-title>
                <h6>
                  <span class="amber--text" v-text="`${1 + index}.`" />
                  <span class="primary--text" v-text="`${item.created_at}`" />
                </h6>
              </v-card-title>
              <v-divider />
              <v-list height="200px">
                <v-list-item>
                  <v-list-item-content v-text="'Items:'" />
                  <v-list-item-content
                    class="align-end primary--text"
                    v-html="`<b><i>${item.item_count}</i></b>`"
                  />
                </v-list-item>
                <v-list-item>
                  <v-list-item-content v-text="'Method:'" />
                  <v-list-item-content
                    class="align-end primary--text"
                    v-html="`<b><i>${item.cart.method}</i></b>`"
                  />
                </v-list-item>
                <v-list-item>
                  <v-list-item-content v-text="'Customer:'" />
                  <v-list-item-content
                    class="align-end primary--text"
                    v-html="
                      `
                      ${
                        item.cart.customer
                          ? '<b><i>' + item.cart.customer.full_name + '</i></b>'
                          : '<b><i>Guest</i></b>'
                      }`
                    "
                  />
                </v-list-item>
              </v-list>
              <v-card-actions class="justify-space-between">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      :loading="deleteLoading[index]"
                      :disabled="isLoading"
                      text
                      color="red"
                      @click.stop="
                        (deleteLoading[index] = true),
                          deleteCart(item.id, index)
                      "
                    >
                      <v-icon v-text="'mdi-delete-outline'" v-on="on" />
                    </v-btn>
                  </template>
                  <span v-text="'Delete'" />
                </v-tooltip>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      text
                      color="primary"
                      :loading="restoreLoading[index]"
                      :disabled="isLoading"
                      @click.stop="
                        (restoreLoading[index] = true), restore(item)
                      "
                    >
                      <v-icon v-on="on" v-text="'mdi-cart-arrow-down'" />
                    </v-btn>
                  </template>
                  <span v-text="'Restore'" />
                </v-tooltip>
              </v-card-actions>
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
        <v-card>
          <v-skeleton-loader type="card-heading" tile class="mx-auto" />
          <v-divider class="my-5" />
          <v-skeleton-loader type="image" tile class="mx-auto" height="200px" />
          <v-card-actions class="justify-space-between">
            <v-skeleton-loader type="button" tile class="" width="40px" />
            <v-skeleton-loader type="button" tile class="" width="40px" />
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col :cols="12">
        <v-skeleton-loader
          type="heading"
          tile
          class="mx-auto d-flex justify-center mt-2"
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
  data() {
    return {
      isLoading: false,
      deleteLoading: [],
      restoreLoading: [],
      cartsLoading: false,
      carts: [],
      currentPage: 1,
      lastPage: null,
      cartReplacePrompt: false
    };
  },

  mounted() {
    this.getCarts();
  },

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    ...mapState("cart", ["cart_products"])
  },

  methods: {
    ...mapActions("requests", ["request"]),
    ...mapActions("cart", ["restoreCart"]),

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
      this.request(payload)
        .then(response => {
          this.currentPage = response.current_page;
          this.lastPage = response.last_page;
          this.carts = response.data;
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.cartsLoading = false;
        });
    },
    restore(cart, index) {
      this.isLoading = true;
      if (_.size(this.cart_products)) {
        this.cartReplacePrompt = true;
      } else {
        this.loadCart(cart);
      }
    },
    loadCart(cart) {
      this.restoreCart(cart)
        .then(() => {
          this.deleteCart(cart.id, null, true);
          this.$emit("submit", true);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    deleteCart(id, index, silent) {
      this.isLoading = true;

      const payload = {
        method: "delete",
        url: `carts/${id}`,
        no_success_notification: silent
      };

      this.request(payload)
        .then(() => {
          this.getCarts();
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.isLoading = false;
          if (index) {
            this.deleteLoading[index] = false;
          }
        });
    }
  }
};
</script>
