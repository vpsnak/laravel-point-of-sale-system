<template>
  <v-container fluid>
    <v-row align="center" justify="center" dense>
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
  </v-container>
</template>
<script>
import { mapState, mapMutations } from "vuex";
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
    ...mapState("cart", ["order_id", "customer"]),

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
        return this.customer;
      },
      set(value) {
        if (!this.order_id) {
          if (value !== this.cartCustomer) {
            this.resetShipping(true);
          }
        }
        this.setCustomer(value);
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
    ...mapMutations("cart", ["setCustomer", "resetShipping"]),

    customerForm(create) {
      this.setDialog({
        show: true,
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
        show: true,
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
        keyword
      };

      this.$store
        .dispatch("search", payload)
        .then(result => {
          this.results = result;
        })
        .catch(error => {
          // undocumented error
          console.error(error);
        })
        .finally(() => (this.loading = false));
    }
  },

  watch: {
    search(keyword) {
      if (keyword && keyword.length >= this.$props.keywordLength) {
        if (this.loading) {
          return;
        } else {
          this.searchCustomer(keyword);
          return;
        }
      }
    }
  }
};
</script>
