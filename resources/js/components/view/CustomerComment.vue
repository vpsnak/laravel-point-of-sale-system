<template>
  <v-textarea
    :value="customerData.comment"
    v-if="customerData"
    readonly
    solo
    auto-grow
    outlined
  ></v-textarea>
  <div v-else>
    <v-progress-circular indeterminate color="primary"></v-progress-circular>
  </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Object
  },
  data() {
    return {
      customer: null
    };
  },
  mounted() {
    if (this.$props.model)
      this.request({
        method: "get",
        url: `customers/get/${this.$props.model.id}`
      }).then(result => {
        this.customer = result;
      });
  },
  computed: {
    customerData() {
      return this.customer;
    }
  },
  methods: {
    ...mapActions("requests", ["request"])
  }
};
</script>
