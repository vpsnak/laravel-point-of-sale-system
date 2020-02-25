<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider
          rules="required"
          v-slot="{ errors, valid }"
          name="Password"
        >
          <v-text-field
            :append-icon="showPassword ? 'visibility' : 'visibility_off'"
            :type="showPassword ? 'text' : 'password'"
            v-model="password"
            label="Password"
            :error-messages="errors"
            :success="valid"
            prepend-icon="mdi-key"
            @click:append="showPassword = !showPassword"
          ></v-text-field>
        </ValidationProvider>
        <v-row>
          <v-col :cols="12">
            <v-text-field
              class="amber--text"
              label="Cash amount"
              :value="amount"
              :loading="loading"
              readonly
              prepend-icon="mdi-currency-usd"
            ></v-text-field>
          </v-col>
        </v-row>
      </v-container>
      <v-container>
        <v-row>
          <v-col cols="6">
            <v-btn
              color="secondary"
              type="submit"
              :loading="loading"
              :disabled="invalid"
            >
              Close Cash Register
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>
<script>
import { mapActions, mapState } from "vuex";

export default {
  data() {
    return {
      loading: false,
      showPassword: false,
      amount: "",
      password: ""
    };
  },
  mounted() {
    this.cashRegisterAmount();
  },
  computed: {
    ...mapState(["cashRegister"])
  },
  methods: {
    ...mapActions(["closeCashRegister"]),
    ...mapActions("requests", ["request"]),

    cashRegisterAmount() {
      this.loading = true;
      this.request({
        method: "get",
        url: `cash-register-logs/${this.cashRegister.id}/amount`
      })
        .then(response => {
          this.amount = response.toFixed(2);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    submit() {
      this.loading = true;
      this.request({
        method: "post",
        url: "auth/verify",
        data: {
          current_password: this.password
        }
      })
        .then(() => {
          let payload = {
            data: {
              cash_register_id: this.cashRegister.id,
              password: this.password
            }
          };
          this.closeCashRegister(payload)
            .then(response => {
              this.$emit("submit", { data: { response } });
            })
            .finally(() => {
              this.loading = false;
            });
        })
        .catch(() => {
          this.password = "";
          this.loading = false;
        });
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
