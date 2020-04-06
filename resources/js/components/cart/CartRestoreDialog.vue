<template>
  <v-container fluid>
    <v-row v-if="!cartsLoading" justify="center">
      <v-col v-if="showWarning" cols="auto">
        <v-alert
          type="warning"
          dense
          border="left"
          colored-border
          :elevation="3"
        >
          Your current cart will be <b>replaced</b>
        </v-alert>
      </v-col>
    </v-row>
    <v-row v-if="!cartsLoading" justify="center" dense>
      <perfect-scrollbar style="height: 375px;" tag="v-col" :cols="12">
        <v-data-iterator
          :items="carts"
          disable-pagination
          disable-filtering
          disable-sort
          hide-default-footer
          no-data-text="No carts found"
        >
          <template v-slot:default="{ items }">
            <v-row dense>
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
                      <span
                        class="primary--text"
                        v-text="`${item.created_at}`"
                      />
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
                          `<b><i>${
                            item.cart.customer
                              ? item.cart.customer.full_name
                              : 'Guest'
                          }</i></b>`
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
        </v-data-iterator>
      </perfect-scrollbar>
      <v-col :cols="12" align="center" justify="end" v-if="carts.length">
        <v-pagination
          v-model="currentPage"
          :length="lastPage"
          @input="getCarts()"
          :total-visible="5"
        />
      </v-col>
    </v-row>
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
import { mapState, mapActions, mapMutations } from "vuex";
import { EventBus } from "../../plugins/eventBus";
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
      cartReplacePrompt: false,
    };
  },

  mounted() {
    this.getCarts();
  },

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    ...mapState("cart", ["cart_products", "customer"]),

    showWarning() {
      if (_.size(this.cart_products) || this.customer) {
        return true;
      } else {
        return false;
      }
    },
  },

  methods: {
    ...mapActions("requests", ["request"]),
    ...mapActions("cart", ["restoreCart"]),

    getCarts() {
      this.cartsLoading = true;
      const payload = {
        method: "get",
        url: `carts?page=${this.currentPage}`,
      };
      this.request(payload)
        .then((response) => {
          this.currentPage = response.current_page;
          this.lastPage = response.last_page;
          this.carts = response.data;
        })
        .catch((error) => {
          console.error(error);
        })
        .finally(() => {
          this.cartsLoading = false;
        });
    },
    restore(cart) {
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
        no_success_notification: silent,
      };

      this.request(payload)
        .then(() => {
          this.getCarts();
          EventBus.$emit("cart-actions-reduce-cart-counter");
        })
        .catch((error) => {
          console.error(error);
        })
        .finally(() => {
          this.isLoading = false;
          if (index) {
            this.deleteLoading[index] = false;
          }
        });
    },
  },
};
</script>
