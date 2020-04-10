<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <v-row justify="center">
          <v-col :cols="6">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors }"
              name="Name"
            >
              <v-text-field
                :disabled="loading"
                :readonly="$props.readonly"
                :error-messages="errors"
                v-model="formFields.name"
                label="Name"
              />
            </ValidationProvider>
          </v-col>
          <v-col :cols="6">
            <ValidationProvider
              rules="required|min:10|max:15"
              v-slot="{ errors }"
              name="Phone"
            >
              <v-text-field
                v-model="formFields.phone"
                type="number"
                :disabled="loading"
                :readonly="$props.readonly"
                :error-messages="errors"
                label="Phone"
              />
            </ValidationProvider>
          </v-col>
          <v-col :cols="6">
            <ValidationProvider
              rules="required|email|max:100"
              v-slot="{ errors }"
              name="Email"
            >
              <v-text-field
                :disabled="loading"
                :readonly="$props.readonly"
                v-model="formFields.email"
                :error-messages="errors"
                label="E-mail"
              />
            </ValidationProvider>
          </v-col>

          <v-col :cols="6">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors }"
              name="Username"
            >
              <v-text-field
                :disabled="loading"
                :readonly="$props.readonly"
                v-model="formFields.username"
                :error-messages="errors"
                label="Username"
              />
            </ValidationProvider>
          </v-col>
          <v-col :cols="6">
            <v-checkbox
              :disabled="loading"
              :readonly="$props.readonly"
              v-model="formFields.active"
              label="Active"
            />
          </v-col>
          <v-col :cols="6">
            <v-text-field
              v-if="$props.readonly"
              :value="formFields.role_name"
              label="Role"
              :disabled="loading"
              readonly
            />
            <ValidationProvider
              v-else
              v-slot="{ errors }"
              name="Role"
              rules="required"
            >
              <v-select
                hide-selected
                v-model="formFields.role_id"
                :items="roles"
                label="Role"
                :error-messages="errors"
                :loading="rolesLoading"
                :disabled="loading"
                item-text="text"
                item-value="id"
              />
            </ValidationProvider>
          </v-col>
        </v-row>
      </v-container>
      <v-container v-if="!$props.readonly">
        <v-row justify="center">
          <v-btn
            type="submit"
            :loading="submitLoading"
            :disabled="invalid || loading"
            color="primary"
            >submit
          </v-btn>
        </v-row>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Object,
    readonly: Boolean
  },

  mounted() {
    this.getAllUserRoles();
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
      this.formFields.role_id = this.$props.model.roles[0].id;
    }
  },

  beforeDestroy() {
    this.$off("submit");
  },

  data() {
    return {
      submitLoading: false,
      rolesLoading: false,
      roles: [],

      formFields: {
        id: null,
        name: null,
        email: null,
        phone: null,
        username: null,
        password: null,
        active: true,
        role_id: null
      }
    };
  },

  computed: {
    disableSubmit() {
      return this.formFields.name ? false : true;
    },
    loading() {
      if (this.submitLoading || this.rolesLoading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    getAllUserRoles() {
      this.rolesLoading = true;

      const payload = {
        method: "get",
        url: "roles"
      };
      this.request(payload)
        .then(response => {
          this.roles = response;
        })
        .finally(() => {
          this.rolesLoading = false;
        });
    },
    submit() {
      this.submitLoading = true;
      const payload = {
        method: this.formFields.id ? "patch" : "post",
        url: this.formFields.id ? "users/update" : "users/create",
        data: this.formFields
      };

      this.request(payload)
        .then(() => {
          this.$emit("submit", {
            action: "paginate"
          });
        })
        .finally(() => {
          this.submitLoading = false;
        });
    }
  }
};
</script>
