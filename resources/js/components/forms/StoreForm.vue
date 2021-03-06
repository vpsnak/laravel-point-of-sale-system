<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh;">
      <v-row>
        <v-col :cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Name"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.name"
              label="Name"
              :disabled="loading"
              :error-messages="errors"
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
              :readonly="$props.readonly"
              v-model="formFields.phone"
              label="Phone"
              :min="0"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Street"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.street"
              label="Street"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Postal code"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.postcode"
              label="Postcode"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="City"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.city"
              label="City"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Company"
          >
            <v-select
              :readonly="$props.readonly"
              v-model="formFields.company_id"
              :items="companies"
              label="Company"
              :disabled="companiesLoading"
              item-text="name"
              item-value="id"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="6">
          <ValidationProvider rules="required" v-slot="{ errors }" name="Tax">
            <v-select
              :readonly="$props.readonly"
              v-model="formFields.tax_id"
              :items="taxes"
              label="Tax"
              :disabled="loading"
              item-text="name"
              item-value="id"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Default currency"
          >
            <v-select
              :items="currencies"
              :readonly="$props.readonly"
              v-model="formFields.default_currency"
              label="Default currency"
              :disabled="loading || true"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row justify="space-around">
        <v-checkbox
          v-model="formFields.active"
          label="Active"
          :readonly="$props.readonly"
          :disabled="loading"
        />
        <v-checkbox
          v-model="formFields.is_phone_center"
          label="Phone center"
          :readonly="$props.readonly"
          :disabled="loading"
        />
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
      companiesLoading: false,
      submitLoading: false,

      currencies: ["USD"],

      valid: true,
      taxes: [],
      companies: [],
      formFields: {
        name: null,
        phone: null,
        street: null,
        postcode: null,
        city: null,
        tax_id: null,
        company_id: null,
        active: true,
        is_phone_center: false,
        default_currency: "USD"
      }
    };
  },

  mounted() {
    this.getAllTaxes();
    this.getAllCompanies();
    if (this.$props.model) {
      this.formFields = { ...this.$props.model };
    }
  },

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    loading() {
      if (this.companiesLoading || this.submitLoading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;
      const payload = {
        method: this.formFields.id ? "patch" : "post",
        url: this.formFields.id ? "stores/update" : "stores/create",
        data: this.formFields
      };

      this.request(payload)
        .then(() => {
          this.$emit("submit", {
            action: "paginate"
          });
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllTaxes() {
      this.loading = true;
      this.request({
        method: "get",
        url: "taxes"
      })
        .then(response => {
          this.taxes = response.data;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllCompanies() {
      this.companiesLoading = true;
      const payload = {
        method: "get",
        url: "companies"
      };
      this.request(payload)
        .then(response => {
          this.companies = response.data;
        })
        .finally(() => {
          this.companiesLoading = false;
        });
    }
  }
};
</script>
