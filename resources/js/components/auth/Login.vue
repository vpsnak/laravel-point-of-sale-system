<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col :cols="12" :sm="8" :md="4" :lg="3">
        <ValidationObserver
          v-slot="{ invalid }"
          tag="v-form"
          @submit.prevent="submit()"
        >
          <v-card class="elevation-12">
            <v-toolbar flat class="d-flex justify-center">
              <v-toolbar-title v-text="app_name" />
            </v-toolbar>
            <v-card-text>
              <ValidationProvider
                rules="required|max:100"
                v-slot="{ errors }"
                name="First Name"
              >
                <v-text-field
                  v-model="username"
                  label="Login"
                  name="login"
                  prepend-inner-icon="person"
                  type="text"
                  :error="errors[0]"
                  single-line
                />
              </ValidationProvider>
              <ValidationProvider
                rules="required|max:100"
                v-slot="{ errors }"
                name="First Name"
              >
                <v-text-field
                  v-model="password"
                  label="Password"
                  prepend-inner-icon="lock"
                  type="password"
                  :error="errors[0]"
                  single-line
                />
              </ValidationProvider>
            </v-card-text>
            <v-card-actions class="justify-center">
              <v-btn
                type="submit"
                :loading="loading"
                :disabled="invalid || loading"
                color="primary"
                text
                outlined
                >Login
              </v-btn>
            </v-card-actions>
          </v-card>
        </ValidationObserver>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions, mapState } from "vuex";

export default {
  data() {
    return {
      username: "",
      password: "",
      loading: false,
    };
  },
  computed: {
    ...mapState("config", ["app_name"]),
  },
  methods: {
    submit() {
      this.loading = true;

      let payload = {
        username: this.username,
        password: this.password,
      };

      this.login(payload)
        .then(() => {
          this.$router.replace({ name: "landingPage" });
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    ...mapActions(["login"]),
  },
};
</script>
