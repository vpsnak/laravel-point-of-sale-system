<template>
    <div class="my-5 d-flex align-center justify-space-around flex-column">
        <h3 v-if="change > 0">
            Change:
            <span class="amber--text" v-text="'$ ' + change" />
        </h3>

        <orderReceipt v-if="order && order.status === 'paid'" />

        <v-btn color="primary" @click="resetState()">Close</v-btn>
    </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
    computed: {
        ...mapState("cart", ["order"]),

        change() {
            if (this.order) {
                return Math.abs(this.order.total - this.order.total_paid);
            }
        }
    },
    methods: {
        ...mapMutations("cart", ["resetState"])
    }
};
</script>
