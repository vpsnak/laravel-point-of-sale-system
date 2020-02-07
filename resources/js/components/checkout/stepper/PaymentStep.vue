<template>
  <div>
    <payment @payment="payment" />

    <v-container>
      <v-row justify="center" align="center">
        <v-col :lg="6" :md="12" justify="center" align="center">
          <h3 v-if="change > 0" class="my-3">
            Change:
            <span class="amber--text" v-text="'$ ' + change" />
          </h3>

          <v-btn
            v-if="order.id && completed && !$store.state.cart.refundLoading"
            color="primary"
            @click="complete"
            :loading="loading"
            :disabled="loading"
          >
            Complete order
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
  data() {
    return {
      completed: false,
      change: 0
    };
  },

  computed: {
    ...mapState("cart", ["order"]),

    loading: {
      get() {
        return this.$store.state.cart.completeOrderLoading;
      },
      set(value) {
        this.$store.state.cart.completeOrderLoading = value;
      }
    }
  },

  methods: {
    ...mapActions(["create"]),
    ...mapActions("cart", ["createReceipt", "completeStep"]),

    payment(payload) {
      if (payload.order_status === "paid") {
        this.completed = true;
      } else {
        this.completed = false;
      }

      this.change = payload.change;
    },
    complete() {
      this.loading = true;
      let payload = {
        model: "orders",
        data: {
          id: this.order.id,
          status: "complete"
        },
        mutation: "cart/setOrder"
      };

      this.create(payload).then(response => {
        this.completeStep().then(() => {
          this.createReceipt(this.order.id).then(response => {
            this.loading = false;
          });
        });
      });
    }
  }
};
</script>
