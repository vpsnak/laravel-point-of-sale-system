<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh;">
      <v-row>
        <v-col cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="First Name"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.first_name"
              label="First name"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Last Name"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.last_name"
              label="Last name"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Address"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.street"
              label="Address"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="max:100"
            v-slot="{ errors }"
            name="Second Address"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.street2"
              label="Second Address"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
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
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
          <ValidationProvider
            rules="required|max:10"
            v-slot="{ errors }"
            name="Zip Code"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.postcode"
              label="Zip Code"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
          <ValidationProvider
            rules="required|max:5"
            v-slot="{ errors }"
            name="State"
          >
            <v-select
              :disabled="loading"
              :readonly="$props.readonly"
              :loading="loading"
              v-model="formFields.region_id"
              :items="regions"
              label="States"
              :error-messages="errors"
              item-text="name"
              item-value="id"
            ></v-select>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
          <ValidationProvider
            rules="required|max:5"
            v-slot="{ errors }"
            name="Countries"
          >
            <v-select
              :disabled="loading"
              :readonly="$props.readonly"
              :loading="loading"
              v-model="formFields.country_id"
              :items="countries"
              label="Countries"
              :error-messages="errors"
              item-text="name"
              item-value="id"
            ></v-select>
          </ValidationProvider>
        </v-col>
        <v-col cols="4">
          <ValidationProvider
            rules="max:100"
            v-slot="{ errors }"
            name="Location"
          >
            <v-select
              v-model="formFields.location"
              label="Locations"
              :items="locations"
              item-text="label"
              item-value="id"
              :disabled="loading"
              :error-messages="errors"
            ></v-select>
          </ValidationProvider>
        </v-col>
        <v-col cols="4">
          <ValidationProvider
            rules="max:100"
            v-slot="{ errors }"
            name="Location name"
          >
            <v-text-field
              v-model="formFields.location_name"
              label="Location name"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="4">
          <ValidationProvider
            :rules="{
              required: true,
              min: 8,
              max: 20,
              regex: /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g,
            }"
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
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="max:100"
            v-slot="{ errors }"
            name="Company"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.company"
              label="Company"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider rules="max:20" v-slot="{ errors }" name="Vat id">
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.vat_id"
              label="Vat id"
              :disabled="loading"
              :error-messages="errors"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row justify="space-around">
        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.is_default_billing"
          label="Default billing"
        ></v-checkbox>
        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.is_default_shipping"
          label="Default shipping"
        ></v-checkbox>
      </v-row>
    </v-container>
    <v-container>
      <v-row v-if="!$props.readonly" align="center" justify="center">
        <v-btn
          class="mr-4"
          type="submit"
          :loading="loading"
          :disabled="invalid || loading"
          color="primary"
          >save
        </v-btn>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
  props: {
    model: Object,
    readonly: Boolean,
  },
  data() {
    return {
      loading: false,
      regions: [],
      countries: [],
      formFields: {
        first_name: null,
        last_name: null,
        street: null,
        street2: null,
        city: null,
        country_id: null,
        region_id: null,
        postcode: null,
        phone: null,
        company: null,
        vat_id: null,
        is_default_billing: false,
        is_default_shipping: false,
        location: null,
        location_name: null,
      },
    };
  },
  mounted() {
    this.getAllRegions();
    this.getAllCountries();
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model,
      };
    }
  },
  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    ...mapState("cart", ["locations"]),
  },

  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;

      if (this.$props.model) {
        this.request({
          method: "patch",
          url: "addresses/update",
          data: { ...this.formFields },
        })
          .then(() => {
            this.$emit("submit", {
              action: "paginate",
            });
          })
          .finally(() => {
            this.loading = false;
          });
      } else {
        this.request({
          method: "post",
          url: "addresses/create",
          data: { ...this.formFields },
        })
          .then(() => {
            this.$emit("submit", {
              action: "paginate",
            });
          })
          .finally(() => {
            this.loading = false;
          });
      }
    },

    getAllRegions() {
      this.loading = true;
      this.request({
        method: "get",
        url: "regions",
      })
        .then((response) => {
          this.regions = response;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllCountries() {
      this.request({
        method: "get",
        url: "countries",
      }).then((response) => {
        this.countries = response;
      });
    },
  },
};
</script>
