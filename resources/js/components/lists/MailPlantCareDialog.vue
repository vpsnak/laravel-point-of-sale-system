<template>
  <v-text-field
    v-model="customer_email"
    label="Send plant care via e-mail"
    prepend-inner-icon="mdi-email-send"
    append-outer-icon="mdi-send"
    @click:append-outer="plantCare"
    :loading="loading"
    :disabled="loading"
  ></v-text-field>
</template>

<script>
import { mapActions, mapState } from "vuex";

export default {
  props: {
    model: Object
  },
  data() {
    return {
      loading: false,
      customer_email: ""
    };
  },
  methods: {
    ...mapActions("requests", ["request"]),

    plantCare() {
      this.loading = true;
      const payload = {
        method: "post",
        url: `mail-plantcare/${this.$props.model.id}`,
        data: {
          email: this.customer_email
        }
      };

      this.request(payload).finally(() => {
        this.loading = false;
      });
    }
  }
};
</script>
