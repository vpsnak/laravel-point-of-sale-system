<template>
  <v-container :key="method">
    <ValidationObserver
      ref="refund-form"
      v-slot="{ valid }"
      tag="v-form"
      @submit.stop="confirmationDialog()"
    >
      <v-row align="center" justify="space-between">
        <v-col md="4" :cols="12">
          <v-switch
            v-model="method"
            class="mx-2"
            label="New giftcard"
            false-value="giftcard-existing"
            true-value="giftcard-new"
          ></v-switch>
        </v-col>
        <v-col :md="4" :cols="6" v-if="method === 'giftcard-new'">
          <ValidationProvider
            name="Gift card name"
            rules="required"
            v-slot="{ errors, valid }"
          >
            <v-text-field
              outlined
              dense
              :error-messages="errors"
              :success="valid"
              autocomplete="off"
              label="Gift card name"
              prepend-inner-icon="mdi-wallet-giftcard"
              v-model="gc_name"
              :disabled="loading"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col :md="4" :cols="6">
          <ValidationProvider
            name="Gift card code"
            rules="required"
            v-slot="{ errors, valid }"
          >
            <v-text-field
              outlined
              dense
              :error-messages="errors"
              :success="valid"
              autocomplete="off"
              label="Gift card code"
              prepend-inner-icon="mdi-numeric"
              v-model="gc_code"
              :disabled="loading"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row justify="center" align="center">
        <v-col :lg="4" :md="6" :cols="12">
          <ValidationProvider
            name="Amount"
            :rules="'required|between:1,' + maxRefund"
            v-slot="{ errors, valid }"
          >
            <v-text-field
              outlined
              dense
              :error-messages="errors"
              :success="valid"
              type="number"
              label="Amount"
              prefix="$"
              v-model="amount"
              :disabled="loading"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row justify="center" align="center">
        <v-col cols="auto">
          <v-btn
            :disabled="!valid"
            type="submit"
            color="primary"
            :loading="loading"
          >
            Refund
          </v-btn>
        </v-col>
      </v-row>
    </ValidationObserver>
  </v-container>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
  mounted() {
    EventBus.$on("order-edit-refund", event => {
      console.log(event);
      if (event) {
        this.submitRefund();
      }
    });
  },
  data() {
    return {
      loading: false,
      method: "giftcard-existing",
      amount: null,
      gc_code: null,
      gc_name: null
    };
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "order_total_paid",
      "order_total",
      "order_status"
    ]),

    maxRefund() {
      if (this.order_status === "paid") {
        return this.order_total;
      } else {
        return this.order_total_paid;
      }
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("requests", ["request"]),

    confirmationDialog() {
      this.setDialog({
        show: true,
        width: 600,
        title: "Verify your password to issue the refund",
        titleCloseBtn: true,
        icon: "mdi-lock-alert",
        component: "passwordForm",
        model: { action: "verify" },
        persistent: true,
        eventChannel: "order-edit-refund"
      });
    },
    submitRefund() {
      this.loading = true;

      let payload = {
        method: "post",
        url: "payments/unlinked-refund",
        mutations: [],
        data: {
          order_id: this.order_id,
          method: this.method,
          amount: this.amount
        }
      };

      switch (this.method) {
        case "giftcard-existing":
          payload.data.existing_gc_code = this.gc_code;
          break;
        case "giftcard-new":
          payload.data.giftcard = {
            code: this.gc_code,
            name: this.gc_name
          };
          break;
        default:
          this.loading = false;
          return;
      }

      this.request(payload)
        .then(response => {
          console.log(response);
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
