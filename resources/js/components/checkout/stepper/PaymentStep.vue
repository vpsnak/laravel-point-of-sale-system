<template>
  <div>
    <payment />

    <v-container>
      <v-row justify="center" align="center">
        <v-col :lg="6" :md="12" justify="center" align="center">
          <h3
            v-if="!changePrice.isZero() && changePrice.isPositive()"
            class="my-3"
          >
            Change:
            <span class="amber--text" v-text="changePrice.toFormat()" />
          </h3>

          <v-btn
            v-if="completed"
            color="primary"
            @click="completeStep()"
            :disabled="checkout_loading"
            outlined
            >Complete order
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
export default {
  data() {
    return {
      completed: false
    };
  },

  watch: {
    order_status: {
      immediate: true,
      handler(status) {
        if (
          _.has(status, "value") &&
          ["paid", "completed"].indexOf(status.value) !== -1
        ) {
          this.completed = true;
        } else {
          this.completed = false;
        }
      }
    }
  },

  computed: {
    ...mapState("cart", [
      "order_status",
      "order_change_price",
      "checkout_loading"
    ]),

    changePrice() {
      if (_.has(this.order_change_price, "amount")) {
        return this.parsePrice(this.order_change_price);
      } else {
        return this.$price();
      }
    }
  },

  methods: {
    ...mapActions("cart", ["createReceipt", "completeStep"])
  }
};
</script>
