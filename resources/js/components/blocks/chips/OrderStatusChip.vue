<template>
  <div>
    <h5 v-if="$props.title">Status</h5>
    <v-chip pill :color="parseStatus.color">
      <v-icon left>{{ parseStatus.icon }}</v-icon>
      {{ parseStatus.name }}
    </v-chip>
  </div>
</template>

<script>
export default {
  props: {
    title: Boolean,
    status: String
  },

  computed: {
    parseStatus() {
      switch (this.$props.status) {
        case "canceled":
          return {
            name: "Canceled",
            icon: "mdi-cancel",
            color: "red"
          };
        case "pending_payment":
          return {
            name: "Pending payment",
            icon: "mdi-currency-usd-off",
            color: "amber--darken-3"
          };
        case "paid":
          return {
            name: "Paid",
            icon: "mdi-currency-usd",
            color: "cyan"
          };

        case "completed":
          return {
            name: "Completed",
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
