<template>
  <v-card class="mx-auto" max-width="500">
    <v-list>
      <v-subheader>
        <v-icon left>mdi-receipt</v-icon>
        <h3>
          Order <i class="cyan--text">#{{ order_id }}</i>
        </h3>
      </v-subheader>
      <v-divider></v-divider>
      <v-list-item @click="printReceipt">
        <v-list-item-icon>
          <v-icon>mdi-printer</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Print Receipt</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item>
        <v-list-item-content>
          <v-text-field
            v-model="customerEmail"
            label="Send Receipt via e-mail"
            prepend-inner-icon="mdi-email-newsletter"
            append-outer-icon="mdi-send"
            @click:append-outer="mailReceipt"
            :loading="loading"
            :disabled="loading"
          ></v-text-field>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
import { mapActions, mapState } from "vuex";

export default {
  data() {
    return {
      loading: false,
      customer_email: ""
    };
  },
  computed: {
    ...mapState("cart", ["order_id", "customer"]),

    customerEmail: {
      get() {
        if (!this.customer_email && this.customer) {
          return this.customer.email;
        } else {
          return this.customer_email;
        }
      },
      set(value) {
        if (this.customer) {
          this.customer.email = value;
        } else {
          this.customer_email = value;
        }
      }
    }
  },
  methods: {
    ...mapActions("cart", ["saveGuestEmail"]),
    ...mapActions("requests", ["request"]),

    printReceipt() {
      window.open(`/receipt/${this.order_id}`, "_blank");
    },
    mailReceipt() {
      this.loading = true;
      let payload = {
        method: "post",
        url: `/guest-email/create`,
        data: { email: this.customerEmail }
      };

      this.request(payload).finally(() => {
        payload.url = `mail-receipt/${this.order_id}`;
        this.request(payload).finally(() => {
          this.loading = false;
        });
      });
    }
  }
};
</script>
