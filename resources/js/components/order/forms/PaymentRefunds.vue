<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>
        mdi-credit-card-refund-outline
      </v-icon>
      Refund
    </v-card-title>
    <v-container>
      <v-row>
        <v-col :cols="12" class="text-center">
          <v-btn-toggle v-model="method" dense mandatory color="primary">
            <v-btn
              v-for="(method, index) in refund_methods"
              :key="index"
              :value="method.value"
            >
              <v-icon left>{{ method.icon }}</v-icon>
              <span>{{ method.text }}</span>
            </v-btn>
          </v-btn-toggle>
        </v-col>
      </v-row>
      <!-- giftcard -->
      <v-row justify="center" v-if="method === 'giftcard'" no-gutters>
        <v-col cols="auto">
          <v-radio-group v-model="existing" row>
            <v-radio label="Existing" :value="true"></v-radio>
            <v-radio label="New" :value="false"></v-radio>
          </v-radio-group>
        </v-col>
      </v-row>
      <v-row dense justify="center">
        <v-col :md="6" v-if="existing">
          <v-text-field
            dense
            autocomplete="off"
            label="Giftcard code"
            type="number"
            prepend-inner-icon="mdi-wallet-giftcard"
            v-model="giftcard_code"
            :disabled="loading"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row justify="center" v-if="method === 'cc-api'">
        <v-col :md="4">
          <v-text-field
            dense
            autocomplete="off"
            label="Card number"
            type="number"
            prepend-inner-icon="mdi-credit-card"
            v-model="card.number"
            :disabled="loading"
          ></v-text-field>
        </v-col>
        <v-col :md="2">
          <v-text-field
            dense
            autocomplete="off"
            :disabled="loading"
            label="Exp date"
            v-model="card.exp_date"
            prepend-inner-icon="mdi-calendar"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row justify="center" v-if="method === 'cc-api'">
        <v-col :md="4">
          <v-text-field
            dense
            autocomplete="off"
            label="Card holder's name"
            prepend-inner-icon="mdi-account-box"
            v-model="card.holder"
            :disabled="loading"
          ></v-text-field>
        </v-col>
        <v-col :md="2">
          <v-text-field
            dense
            autocomplete="off"
            label="CVC/CVV"
            type="number"
            prepend-inner-icon="mdi-lock"
            v-model="card.cvc"
            :disabled="loading"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row justify="center" justify-md="start" align="center">
        <v-col :md="2" :offset-md="3">
          <v-text-field
            v-model="amount"
            label="Amount"
            prefix="$"
          ></v-text-field>
        </v-col>
        <v-col :md="4" :offset-md="2">
          <v-btn
            @click="submitRefund()"
            text
            color="primary"
            :disabled="!valid"
          >
            Refund
            <v-icon right>
              mdi-send-outline
            </v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  data() {
    return {
      existing: true,
      giftcard_code: null,
      refund_methods: [
        {
          text: "Gift card",
          icon: "mdi-wallet-giftcard",
          value: "giftcard"
        },
        {
          text: "CC API",
          icon: "mdi-credit-card",
          value: "cc-api"
        },
        {
          text: "CC",
          icon: "mdi-credit-card-scan",
          value: "cc-pos"
        }
      ],
      valid: false,
      method: null,
      loading: false,
      amount: null,
      card: {
        number: "",
        holder: "",
        exp_date: "",
        cvc: ""
      }
    };
  },

  computed: {
    ...mapState("cart", ["order"]),

    limits() {
      return;
    }
  },

  methods: {
    ...mapActions("requests", ["post"]),

    submitRefund() {
      let payload = {
        endpoint: "unlinked-refund",
        success_notification: true,
        error_notification: true,
        mutations: [],
        data: {
          amount
        }
      };
      existing;
      giftcard_code;

      card;
      switch (this.method) {
        case "giftcard":
          break;
        case "cc-api":
          break;
        case "cc-pos":
          payload.data.card = this.card;
          break;
        default:
          return;
      }

      this.post(payload);
    }
  }
};
</script>
