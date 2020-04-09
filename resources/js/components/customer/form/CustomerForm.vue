<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh;">
      <v-row align="center">
        <v-col :cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="First name"
          >
            <v-text-field
              v-model="formFields.first_name"
              label="First name"
              :error-messages="errors"
              :readonly="$props.readonly"
              :disabled="loading"
              dense
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Last Name"
          >
            <v-text-field
              v-model="formFields.last_name"
              label="Last name"
              :error-messages="errors"
              :readonly="$props.readonly"
              :disabled="loading"
              dense
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
              v-model="formFields.email"
              label="Email"
              :error-messages="errors"
              :readonly="$props.readonly"
              :disabled="loading"
              dense
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            rules="required|min:8|max:16"
            v-slot="{ errors }"
            name="Phone"
          >
            <v-text-field
              v-model="formFields.phone"
              label="Phone"
              :min="0"
              :disabled="loading"
              :error-messages="errors"
              :readonly="$props.readonly"
              dense
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="4" :lg="2">
          <v-checkbox
            v-model="formFields.house_account_status"
            :disabled="loading"
            label="House account"
            :readonly="$props.readonly"
            @change="house_account_status = formFields.house_account_status"
            dense
          />
        </v-col>
        <v-col :cols="4" :lg="4">
          <ValidationProvider
            :rules="house_account_status ? 'required|max:100' : 'max:100'"
            v-slot="{ errors }"
            name="House account number"
          >
            <v-text-field
              v-model="formFields.house_account_number"
              :disabled="loading"
              label="House account number"
              :error-messages="errors"
              :readonly="$props.readonly"
              dense
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="4" :lg="6">
          <ValidationProvider
            :rules="
              house_account_status
                ? 'required|between:0.01,10000'
                : 'between:0.01,10000'
            "
            v-slot="{ errors }"
            name="House account limit"
          >
            <v-text-field
              v-model="formFields.house_account_limit"
              :disabled="loading"
              label="House account limit"
              type="number"
              :error-messages="errors"
              :readonly="$props.readonly"
              dense
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="6">
          <v-checkbox
            v-model="formFields.no_tax"
            :disabled="loading"
            label="Zero tax"
            :readonly="$props.readonly"
            dense
          />
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            v-if="formFields.no_tax"
            rules="ext:jpg,png,jpeg,pdf"
            v-slot="{ errors }"
            name="Certification file"
            dense
          >
            <v-file-input
              v-model="formFields.file"
              :readonly="$props.readonly"
              :error-messages="errors"
              :disabled="loading"
              label="Upload certification file"
              accept="image/png, image/jpeg, application/pdf"
              show-size
              clearable
              dense
            />
          </ValidationProvider>
        </v-col>

        <v-col v-if="formFields.no_tax_file" :cols="6">
          <v-btn
            v-if="formFields.no_tax_file"
            :href="formFields.no_tax_file"
            :disabled="loading"
            text
            target="_blank"
            v-text="'View uploaded file'"
          />
        </v-col>
      </v-row>
    </v-container>
    <v-container v-if="!$props.readonly">
      <v-row align="center" justify="center">
        <v-btn
          color="primary"
          class="mr-4"
          type="submit"
          :loading="loading"
          :disabled="invalid || loading"
        >
          <span v-text="stepper ? 'next' : 'save'" />
        </v-btn>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>
<script>
import { mapState, mapActions } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    model: Object,
    readonly: Boolean,
    stepper: Boolean
  },

  data() {
    return {
      loading: false,
      house_account_status: false,

      formFields: {
        first_name: null,
        last_name: null,
        email: null,
        house_account_number: null,
        house_account_limit: null,
        house_account_status: false,
        no_tax: false,
        file: null,
        no_tax_file: null
      }
    };
  },

  mounted() {
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    makeFormData() {
      let data = new FormData();
      data.append("id", this.formFields.id);
      data.append("first_name", this.formFields.first_name);
      data.append("last_name", this.formFields.last_name);
      data.append("email", this.formFields.email);
      if (this.formFields.house_account_status) {
        data.append(
          "house_account_number",
          this.formFields.house_account_number
        );
        data.append("house_account_limit", this.formFields.house_account_limit);
        data.append(
          "house_account_status",
          this.formFields.house_account_status ? 1 : 0
        );
      }
      data.append("no_tax", this.formFields.no_tax ? 1 : 0);
      data.append("file", this.formFields.file);
      data.append("comment", this.formFields.comment);

      return data;
    },
    submit() {
      this.loading = true;
      let data;
      if (this.formFields.no_tax && this.formFields.file) {
        data = this.makeFormData();
      } else {
        data = this.formFields;
      }

      const payload = {
        method: _.has(this.$props.model, "id") ? "patch" : "post",
        url: _.has(this.$props.model, "id")
          ? "customers/update"
          : "customers/create",
        data: data
      };
      this.request(payload)
        .then(response => {
          console.log(response);
          EventBus.$emit("customer-form-submit", response.customer);
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
