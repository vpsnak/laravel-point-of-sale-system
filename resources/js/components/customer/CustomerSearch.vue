<template>
  <div>
    <v-row align="center" justify="center">
      <v-combobox
        :no-filter="true"
        v-if="editable"
        v-model="cartCustomer"
        clearable
        dense
        :items="results"
        :loading="loading"
        :search-input.sync="search"
        hide-no-data
        hide-selected
        :item-text="getCustomerFullname"
        label="Select customer"
        placeholder="Start typing to Search"
        prepend-icon="mdi-account-search"
        return-object
        @blur="checkIfObjectEvent"
      ></v-combobox>

      <v-text-field
        v-else
        :value="getCustomerFullname(cartCustomer)"
        disabled
        prepend-icon="person"
      ></v-text-field>

      <v-tooltip bottom v-if="cartCustomer">
        <template v-slot:activator="{ on }">
          <v-btn
            class="mx-1"
            @click.stop="customerForm(false)"
            v-on="on"
            icon
            small
            :disabled="!editable"
          >
            <v-icon small>mdi-pencil</v-icon>
          </v-btn>
        </template>
        <span>View / Edit selected customer</span>
      </v-tooltip>

      <v-tooltip bottom v-if="cartCustomerComment" color="red">
        <template v-slot:activator="{ on }">
          <v-btn
            class="mx-1"
            @click.stop="viewCustomerComments"
            text
            icon
            color="red"
            small
            v-on="on"
          >
            <v-icon small>mdi-comment</v-icon>
          </v-btn>
        </template>
        <span>View comments</span>
      </v-tooltip>

      <v-divider inset vertical class="mx-2"></v-divider>

      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn
            class="mx-1"
            @click.stop="customerForm(true)"
            v-on="on"
            icon
            :disabled="!editable"
            small
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
        <span>Add a customer</span>
      </v-tooltip>
    </v-row>
  </div>
</template>
<script>
import { mapMutations } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
  mounted() {
    EventBus.$on("customer-search", event => {
      if (event.payload.customer) {
        this.cartCustomer = event.payload.customer;
      }
    });
  },

  beforeDestroy() {
    EventBus.$off();
  },

  props: {
    keywordLength: Number,
    editable: Boolean
  },

  data() {
    return {
      loading: false,
      search: null,
      showCustomerComments: false,
      showCreateDialog: false,
      customers: []
    };
  },
  computed: {
    results: {
      get() {
        return this.customers;
      },
      set(value) {
        this.customers = value;
      }
    },
    cartCustomer: {
      get() {
        if (this.$store.state.cart.order.id) {
          return this.$store.state.cart.order.customer || {};
        } else {
          return this.$store.state.cart.customer;
        }
      },
      set(value) {
        if (!this.$store.state.cart.order.id) {
          if (value !== this.cartCustomer) {
            this.$store.commit("cart/resetShipping");
          }
          this.$store.commit("cart/setCustomer", value);
        }
      }
    },
    cartCustomerComment() {
      if (this.cartCustomer) {
        return this.cartCustomer.comment;
      } else {
        return false;
      }
    }
  },
  methods: {
    ...mapMutations("dialog", ["setDialog"]),

    customerForm(create) {
      this.setDialog({
        width: 600,
        fullscreen: false,
        icon: "mdi-account-plus",
        title: create
          ? "Add new customer"
          : `View / Edit customer #${this.cartCustomer.id}`,
        titleCloseBtn: true,
        component: create ? "customerNewForm" : "customerForm",
        model: create ? {} : this.cartCustomer,
        persistent: create ? true : false,
        eventChannel: "customer-search"
      });
    },
    viewCustomerComments() {
      this.setDialog({
        width: 600,
        fullscreen: false,
        icon: "mdi-comment",
        title: `Comments for ${this.cartCustomer.full_name}`,
        titleCloseBtn: true,
        component: "customerComment",
        model: this.cartCustomer,
        persistent: false
      });
    },
    checkIfObjectEvent() {
      if (!_.isObjectLike(this.cartCustomer)) {
        this.search = null;
      }
    },
    getCustomerFullname(item) {
      if (_.has(item, "full_name")) {
        return `${item.full_name}`;
      } else {
        return "Guest";
      }
    },
    searchCustomer(keyword) {
      this.loading = true;
      const payload = {
        model: "customers",
        keyword: keyword
      };

      this.$store
        .dispatch("search", payload)
        .then(result => {
          this.results = result;
        })
        .catch(error => {
          // undocumented error
          console.log(error.response);
        })
        .finally(() => (this.loading = false));
    }
  },
  watch: {
    search(keyword) {
      if (keyword) {
        if (keyword.length >= this.$props.keywordLength) {
          if (this.loading) {
            return;
          } else {
            this.searchCustomer(keyword);
            return;
          }
        }
      }
    }
  }
};
</script>
