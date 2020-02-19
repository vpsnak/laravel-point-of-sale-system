<template>
  <v-card outlined :disabled="!maxRefund">
    <v-card-title>
      <v-icon left>
        mdi-credit-card-refund-outline
      </v-icon>
      Refund
    </v-card-title>
    <v-container :key="method">
      <ValidationObserver
        ref="refund-form"
        v-slot="{ valid, invalid }"
        tag="v-form"
        @submit.stop="submitRefund"
      >
        <v-row>
          <v-col :cols="12" class="text-center">
            <v-btn-toggle v-model="method" dense mandatory color="primary">
              <v-btn
                v-for="(refund_method, index) in refund_methods"
                :key="index"
                :value="refund_method.value"
              >
                <v-icon left>{{ refund_method.icon }}</v-icon>
                <span>{{ refund_method.text }}</span>
              </v-btn>
            </v-btn-toggle>
          </v-col>
        </v-row>
        <!-- giftcard-existing -->
        <v-row dense justify="center" v-if="method === 'giftcard-existing'">
          <v-col :md="4">
            <ValidationProvider
              name="Gift card code"
              rules="required"
              v-slot="{ errors, valid }"
            >
              <v-text-field
                dense
                :error-messages="errors"
                :success="valid"
                autocomplete="off"
                label="Gift card code"
                prepend-inner-icon="mdi-wallet-giftcard"
                v-model="existing_gc_code"
                :disabled="loading"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <!-- giftcard-new -->
        <v-row justify="center" v-else-if="method === 'giftcard-new'">
          <v-col :md="3">
            <ValidationProvider
              name="Gift card code"
              rules="required"
              v-slot="{ errors, valid }"
            >
              <v-text-field
                dense
                :error-messages="errors"
                :success="valid"
                autocomplete="off"
                label="Gift card code"
                prepend-inner-icon="mdi-wallet-giftcard"
                v-model="giftcard.code"
                :disabled="loading"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col :md="3">
            <ValidationProvider
              name="Gift card name"
              rules="required"
              v-slot="{ errors, valid }"
            >
              <v-text-field
                dense
                :error-messages="errors"
                :success="valid"
                autocomplete="off"
                label="Gift card name"
                prepend-inner-icon="mdi-wallet-giftcard"
                v-model="giftcard.name"
                :disabled="loading"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <v-row justify="center" v-if="method === 'cc-api'">
          <v-col :md="4">
            <ValidationProvider
              name="Card number"
              rules="required"
              v-slot="{ errors, valid }"
            >
              <v-text-field
                dense
                :error-messages="errors"
                :success="valid"
                autocomplete="off"
                label="Card number"
                type="number"
                prepend-inner-icon="mdi-credit-card"
                v-model="card.number"
                :disabled="loading"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col :md="2">
            <ValidationProvider
              name="Exp date"
              rules="required"
              v-slot="{ errors, valid }"
            >
              <v-text-field
                dense
                :error-messages="errors"
                :success="valid"
                autocomplete="off"
                :disabled="loading"
                label="Exp date"
                v-model="card.exp_date"
                prepend-inner-icon="mdi-calendar"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <v-row justify="center" v-if="method === 'cc-api'">
          <v-col :md="4">
            <ValidationProvider rules="required" v-slot="{ errors, valid }">
              <v-text-field
                dense
                :error-messages="errors"
                :success="valid"
                autocomplete="off"
                label="Card holder's name"
                prepend-inner-icon="mdi-account-box"
                v-model="card.holder"
                :disabled="loading"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col :md="2">
            <ValidationProvider rules="required" v-slot="{ errors, valid }">
              <v-text-field
                dense
                :error-messages="errors"
                :success="valid"
                autocomplete="off"
                label="CVC/CVV"
                type="number"
                prepend-inner-icon="mdi-lock"
                v-model="card.cvc"
                :disabled="loading"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <v-row justify="center" justify-md="start" align="center">
          <v-col :md="2" :offset-md="3">
            <ValidationProvider
              :rules="'required|between:0.1,' + maxRefund"
              v-slot="{ errors, valid }"
              name="Amount"
            >
              <v-text-field
                :error-messages="errors"
                :success="valid"
                v-model="amount"
                :disabled="loading"
                type="number"
                label="Amount"
                prefix="$"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col :md="4" :offset-md="2">
            <v-btn
              type="submit"
              text
              color="primary"
              :disabled="invalid"
              :loading="loading"
            >
              Refund
            </v-btn>
          </v-col>
        </v-row>
      </ValidationObserver>
    </v-container>
  </v-card>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  data() {
    return {
      loading: false,
      refund_methods: [
        {
          id: 7,
          text: "Existing gift card",
          icon: "mdi-wallet-giftcard",
          value: "giftcard-existing"
        },
        {
          id: 8,
          text: "New gift card",
          icon: "mdi-wallet-giftcard",
          value: "giftcard-new"
        },
        {
          id: 9,
          text: "CC API",
          icon: "mdi-credit-card",
          value: "cc-api"
        },
        {
          id: 10,
          text: "CC",
          icon: "mdi-credit-card-scan",
          value: "cc-pos"
        }
      ],
      method: "giftcard-existing",
      amount: null,
      existing_gc_code: null,
      giftcard: {
        code: null,
        name: null
      },
      card: {
        number: "",
        holder: "",
        exp_date: "",
        cvc: ""
      }
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
    ...mapActions("requests", ["post"]),

    submitRefund() {
      this.loading = true;

      let payload = {
        endpoint: "payments/unlinked-refund",
        success_notification: true,
        error_notification: true,
        mutations: [],
        data: {
          order_id: this.order_id,
          method: this.method,
          amount: this.amount
        }
      };

      switch (this.method) {
        case "giftcard-existing":
          payload.data.existing_gc_code = this.existing_gc_code;
          break;
        case "giftcard-new":
          payload.data.giftcard = this.giftcard;
          break;
        case "cc-api":
          break;
        case "cc-pos":
          payload.data.card = this.card;
          break;
        default:
          return;
      }

      this.post(payload)
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
