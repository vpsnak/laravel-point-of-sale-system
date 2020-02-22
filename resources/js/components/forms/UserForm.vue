<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider
          rules="required|max:191"
          v-slot="{ errors, valid }"
          name="Name"
        >
          <v-text-field
            :readonly="$props.readonly"
            :error-messages="errors"
            :success="valid"
            v-model="formFields.name"
            label="Name"
          ></v-text-field>
        </ValidationProvider>

        <ValidationProvider
          rules="required|email|max:191"
          v-slot="{ errors, valid }"
          name="Email"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.email"
            :error-messages="errors"
            :success="valid"
            label="E-mail"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          :rules="{
            required: true,
            min: 8,
            max: 191,
            regex: /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g
          }"
          v-slot="{ errors, valid }"
          name="Phone"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.phone"
            :error-messages="errors"
            :success="valid"
            label="Phone"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|max:191"
          v-slot="{ errors, valid }"
          name="Username"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.username"
            :error-messages="errors"
            :success="valid"
            label="Username"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          v-if="!model"
          rules="required|min:8|max:191"
          v-slot="{ errors, valid }"
          name="Password"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.password"
            :append-icon="showPassword ? 'visibility' : 'visibility_off'"
            :type="showPassword ? 'text' : 'password'"
            :error-messages="errors"
            :success="valid"
            name="input-10-1"
            label="Password"
            hint="At least 8 characters"
            counter
            @click:append="showPassword = !showPassword"
          ></v-text-field>
        </ValidationProvider>
        <v-checkbox v-model="formFields.active" label="Active"></v-checkbox>
      </v-container>
      <v-container>
        <v-row v-if="!$props.readonly">
          <v-col cols="12" align="center" justify="center">
            <v-btn
              class="mr-4"
              type="submit"
              :loading="loading"
              :disabled="invalid || disableSubmit"
              color="secondary"
              >submit
            </v-btn>
            <v-btn v-if="!model" @click="clear" color="orange">clear</v-btn>
          </v-col>
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
  data() {
    return {
      loading: false,
      showPassword: false,
      defaultValues: {},
      formFields: {
        id: null,
        name: null,
        email: null,
        phone: null,
        username: null,
        password: null,
        active: true
      }
    };
  },
  computed: {
    disableSubmit() {
      return this.formFields.name ? false : true;
    }
  },
  mounted() {
    this.defaultValues = { ...this.formFields };
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
    }
  },
  methods: {
    ...mapActions({
      create: "create"
    }),

    submit() {
      this.loading = true;
      let payload = {
        model: "users",
        data: { ...this.formFields }
      };

      if (this.$props.model) {
        axios
          .patch(`/api/users/update/${this.$props.model.id}`, payload.data)
          .then(() => {
            this.clear();
            this.$emit("submit", {
              action: "paginate",
              notification: {
                msg: "User added successfully",
                type: "success"
              }
            });
          })
          .finally(() => {
            this.loading = false;
          });
      } else {
        this.create(payload)
          .then(() => {
            this.clear();
            this.$emit("submit", {
              action: "paginate",
              notification: {
                msg: "User added successfully",
                type: "success"
              }
            });
          })
          .finally(() => {
            this.loading = false;
          });
      }
    },
    clear() {
      this.formFields = { ...this.defaultValues };
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
