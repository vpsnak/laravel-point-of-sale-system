<template>
  <div>
    <h5 v-if="$props.title">MAS Status</h5>
    <v-chip pill :color="parseMasStatus.color">
      <v-icon left>
        {{ parseMasStatus.icon }}
      </v-icon>
      {{ parseMasStatus.name }}
    </v-chip>
  </div>
</template>

<script>
export default {
  props: {
    title: Boolean,
    mas_order: Object
  },

  computed: {
    parseMasStatus() {
      switch (this.$props.mas_order.status) {
        case "canceled":
          return {
            name: "Canceled",
            icon: "mdi-cancel",
            color: "red"
          };
        case "queued":
          return {
            name: "In queue",
            icon: "",
            color: "deep-orange"
          };
        case "submitted":
          return {
            name: "Submitted",
            icon: "",
            color: "deep-orange"
          };
        case "complete":
          return {
            name: "Complete",
            icon: "mdi-cancel",
            color: "green"
          };
        default:
          return {
            name: "huh?",
            icon: "mdi-file-question-outline",
            color: null
          };
      }
    }
  }
};
</script>

<style></style>
