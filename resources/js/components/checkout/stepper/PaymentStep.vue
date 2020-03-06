<template>
  <div>
    <payment />

    <v-container>
      <v-row justify="center" align="center">
        <v-col :lg="6" :md="12" justify="center" align="center">
          <h3 v-if="order_change > 0" class="my-3">
            Change:
            <span class="amber--text" v-text="'$ ' + order_change" />
          </h3>

          <v-btn
            v-if="completed"
            color="success"
            @click="complete"
            :loading="loading"
            :disabled="loading"
            text
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
    order_status(value) {
      if (value === "paid") {
        this.completed = true;
      } else {
        this.completed = false;
      }
    }
  },

  computed: {
    ...mapState("cart", ["order_id", "order_status", "order_change"]),

    loading() {
      return false;
    }
  },

  methods: {
    ...mapActions("cart", ["createReceipt", "completeStep"]),

    complete() {
      this.loading = true;

      this.createReceipt(this.order_id)
        .then(() => {
          this.completeStep();
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
