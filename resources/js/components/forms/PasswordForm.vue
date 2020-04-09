<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
      <v-row>
        <v-col
          v-if="['change_self', 'verify'].indexOf($props.action) !== -1"
          :cols="12"
        >
          <ValidationProvider
            rules="required|min:8|max:20"
            v-slot="{ errors }"
            name="Password"
          >
            <v-text-field
              v-model="formFields.current_password"
              :append-icon="
                showCurrentPassword ? 'visibility' : 'visibility_off'
              "
              :type="showCurrentPassword ? 'text' : 'password'"
              :error-messages="errors"
              label="Password"
              hint="At least 8 characters"
              counter
              @click:append="showCurrentPassword = !showCurrentPassword"
            />
          </ValidationProvider>
        </v-col>
        <v-col
          v-if="['change_self', 'change'].indexOf($props.action) !== -1"
          :cols="12"
        >
          <ValidationProvider
            v-slot="{ errors }"
            rules="required|min:8|max:20"
            name="New Password"
            vid="confirmation"
          >
            <v-text-field
              v-model="formFields.password"
              :append-icon="showPassword ? 'visibility' : 'visibility_off'"
              :type="showPassword ? 'text' : 'password'"
              :error-messages="errors"
              name="input-10-1"
              label="New Password"
              hint="At least 8 characters"
              counter
              @click:append="showPassword = !showPassword"
            />
          </ValidationProvider>
        </v-col>
        <v-col
          v-if="['change_self', 'change'].indexOf($props.action) !== -1"
          :cols="12"
        >
          <ValidationProvider
            rules="required|min:8|max:20|confirmed:confirmation"
            v-slot="{ errors }"
            name="Password Confirmation"
          >
            <v-text-field
              v-model="formFields.password_confirmation"
              :append-icon="
                showPasswordConfirmation ? 'visibility' : 'visibility_off'
              "
              :type="showPasswordConfirmation ? 'text' : 'password'"
              :error-messages="errors"
              name="input-10-1"
              label="Retype the new password"
              counter
              @click:append="
                showPasswordConfirmation = !showPasswordConfirmation
              "
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12" v-if="$props.action === 'change'">
          <v-checkbox label="Deauthorize" v-model="formFields.deauth" />
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <v-row align="center" justify="center">
        <v-btn
          class="mt-2"
          type="submit"
          color="primary"
          outlined
          :loading="loading"
          :disabled="invalid || disableSubmit"
          >submit
        </v-btn>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    action: String
  },

  beforeDestroy() {
    this.$off("submit");
  },

  data() {
    return {
      showCurrentPassword: false,
      showPassword: false,
      showPasswordConfirmation: false,
      loading: false,
      formFields: {
        current_password: null,
        password: null,
        password_confirmation: null,
        deauth: false
      }
    };
  },

  computed: {
    disableSubmit() {
      switch (this.$props.action) {
        case "change_self":
          return this.formFields.current_password ? false : true;
        case "change":
          return this.formFields.password ? false : true;
        case "verify":
          return this.formFields.current_password ? false : true;
        default:
          return true;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      switch (this.$props.action) {
        case "change_self":
          this.loading = true;
          this.request({
            method: "post",
            url: "auth/password",
            data: this.formFields
          })
            .then(() => {
              this.$emit("submit", true);
            })
            .finally(() => {
              this.loading = false;
            });
          break;
        case "change":
          this.loading = true;
          this.formFields.user_id = this.$props.model.id;
          this.request({
            method: "post",
            url: "users/password",
            data: this.formFields
          })
            .then(() => {
              this.$emit("submit", true);
            })
            .finally(() => {
              this.loading = false;
            });
          break;
        case "verify":
          this.loading = true;
          this.request({
            method: "post",
            url: "auth/verify",
            data: this.formFields
          })
            .then(() => {
              this.$emit("submit", true);
            })
            .finally(() => {
              this.loading = false;
            });
          break;
      }
    }
  }
};
</script>
