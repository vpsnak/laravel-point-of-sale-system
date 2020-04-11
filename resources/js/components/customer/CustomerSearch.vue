<template>
  <v-row align="center" class="mx-3">
    <v-combobox
      v-if="editable"
      v-model="cartCustomer"
      solo
      loader-height="5"
      class="pt-5 mt-2"
      ref="searchfield"
      no-filter
      clearable
      outlined
      dense
      :items="results"
      :loading="loading"
      :search-input.sync="search"
      hide-no-data
      hide-selected
      :item-text="getCustomerFullname"
      label="Search customer"
      placeholder="Search customer"
      prepend-inner-icon="mdi-account-search"
      return-object
      @blur="checkIfObjectEvent"
      hint="Results are limited to 15"
    />

    <v-text-field
      v-else
      :value="getCustomerFullname(cartCustomer)"
      disabled
      prepend-icon="person"
    />

    <v-tooltip bottom v-if="cartCustomer">
      <template v-slot:activator="{ on }">
        <v-btn
          class="mx-2"
          @click.stop="customerForm(false)"
          v-on="on"
          text
          :disabled="!editable"
          small
        >
          <v-icon v-text="'mdi-pencil'" />
        </v-btn>
      </template>
      <span v-text="'View / Edit selected customer'" />
    </v-tooltip>

    <v-tooltip bottom v-if="cartCustomerComment">
      <template v-slot:activator="{ on }">
        <v-btn
          class="mx-2"
          @click.stop="viewCustomerComments"
          text
          color="red"
          v-on="on"
          small
        >
          <v-icon v-text="'mdi-comment'" />
        </v-btn>
      </template>
      <span v-text="'View comments'" />
    </v-tooltip>

    <v-tooltip bottom>
      <template v-slot:activator="{ on }">
        <v-btn
          class="mx-2"
          @click.stop="customerForm(true)"
          v-on="on"
          text
          :disabled="!editable"
          small
        >
          <v-icon v-text="'mdi-plus'" />
        </v-btn>
      </template>
      <span v-text="'Add a customer'" />
    </v-tooltip>
    <v-divider vertical class="mx-1" />
    <cartMethods v-if="$props.showMethods" />
  </v-row>
</template>
<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../plugins/eventBus";

export default {
  props: {
    showMethods: Boolean,
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

  mounted() {
    EventBus.$on("customer-search", event => {
      if (event.payload.customer) {
        this.cartCustomer = event.payload.customer;
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("customer-search");
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
        if (!this.order_id && value !== this.customer && this.results.length) {
          this.resetDelivery(true);
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
    ...mapActions("requests", ["request"]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", ["setCustomer", "resetDelivery"]),

    customerForm(create) {
      const payload = {
        show: true,
        width: 600,

        icon: "mdi-account-plus",
        title: create
          ? "Add new customer"
          : `View / Edit customer #${this.cartCustomer.id}`,
        titleCloseBtn: true,
        component: create ? "customerCreateStepper" : "customerForm",
        component_props: {
          model: create ? {} : this.cartCustomer
        },
        no_padding: true,
        persistent: create ? true : false,
        eventChannel: "customer-search"
      };
      this.setDialog(payload);
    },
    viewCustomerComments() {
      const payload = {
        show: true,
        width: 600,

        icon: "mdi-comment",
        title: `Comments for ${this.cartCustomer.full_name}`,
        titleCloseBtn: true,
        component: "customerComment",
        component_props: { model: this.cartCustomer },
        persistent: false
      };
      this.setDialog(payload);
    },
    checkIfObjectEvent() {
      if (!_.isObjectLike(this.cartCustomer)) {
        this.search = null;
      }
    },
    getCustomerFullname(item) {
      if (_.has(item, "full_name")) {
        return item.full_name;
      } else {
        return "Guest";
      }
    },
    searchCustomer(keyword) {
      this.loading = true;

      const payload = {
        method: "post",
        url: "customers/search",
        data: { keyword: keyword, items: 15 }
      };
      this.request(payload)
        .then(result => {
          this.results = result.data;
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.loading = false;
        });
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
    },
    customer(value) {
      if (!value) {
        this.$refs.searchfield.lazySearch = this.$refs.searchfield.lazyValue = null;
      }
    }
  }
};
</script>
