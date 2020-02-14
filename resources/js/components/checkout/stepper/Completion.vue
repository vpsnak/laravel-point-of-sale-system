<template>
  <div class="my-5 d-flex align-center justify-space-around flex-column">
    <h3 v-if="order_change > 0">
      Change:
      <span class="amber--text" v-text="'$ ' + order_change" />
    </h3>

    <orderReceipt v-if="order_status === 'paid'" />

    <v-btn color="primary" @click="resetState()">Close</v-btn>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
  computed: {
    ...mapState("cart", ["order_status", "order_change"])
  },
  methods: {
    ...mapMutations("cart", ["resetState"])
  }
};
</script>
