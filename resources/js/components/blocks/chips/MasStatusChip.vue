<template>
  <div>
    <h5 v-if="$props.title">MAS Status</h5>
    <v-chip label :color="parseMasStatus.color" :small="small">
      <v-icon left>
        {{ parseMasStatus.icon }}
      </v-icon>
      <b>{{ parseMasStatus.name }}</b>
    </v-chip>
  </div>
</template>

<script>
export default {
  props: {
    small: Boolean,
    title: Boolean,
    mas_order: Object
  },

  computed: {
    parseMasStatus() {
      if (this.$props.mas_order) {
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
      } else {
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
