<template>
  <div>
    <v-chip-group multiple column active-class="primary--text">
      <v-chip
        v-for="cartOnHold in cartsOnHold"
        :key="cartOnHold.id"
        close
        @click="(selectedCart = cartOnHold), restoreCart()"
        @click:close="(selectedCart = cartOnHold), removeCart()"
      >
        <span v-tezt="cartOnHold.name" />
      </v-chip>
    </v-chip-group>

    <v-card-actions class="justify-center">
      <v-btn color="primary" text @click="close">Close</v-btn>
    </v-card-actions>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    show: Boolean
  },

  beforeDestroy() {
    this.$off("close");
  },

  data() {
    return {
      onHold: [],
      cart_replace_prompt: false,
      selectedCart: {}
    };
  },

  computed: {
    cartReplacePrompt: {
      get() {
        return this.cart_replace_prompt;
      },
      set(value) {
        this.cart_replace_prompt = value;
      }
    },
    cartsOnHold: {
      get() {
        return this.onHold;
      },
      set(value) {
        this.onHold = value;
      }
    }
  },

  mounted() {
    this.getCartsOnHold();
  },

  methods: {
    ...mapActions("requests", ["request"]),

    confirmation(event) {
      if (event) {
        this.loadCart();
      }

      this.cartReplacePrompt = false;
    },
    getCartsOnHold() {
      const payload = {
        method: "get",
        url: "carts"
      };
      this.request(payload).then(response => {
        this.cartsOnHold = response.data;
      });
    },
    close() {
      this.$emit("close");
    },
    restoreCart() {
      if (_.size(this.$store.state.cart.products)) {
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

      const payload = {
        method: "get",
        url: `customers/get/${JSON.parse(this.selectedCart.cart).customer_id}`
      };
      this.request(payload).then(response => {
        this.removeCart();
        this.close();
      });
    },
    removeCart() {
      const payload = {
        method: "delete",
        url: `carts/${this.selectedCart.id}`
      };

      return this.request(payload).then(() => {
        this.cartsOnHold = this.cartsOnHold.filter(cart => {
          return cart.id !== this.selectedCart.id;
        });
      });
    }
  }
};
</script>
