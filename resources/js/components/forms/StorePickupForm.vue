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
            v-model="formFields.name"
            label="Name"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required"
          v-slot="{ errors, valid }"
          name="Regions"
        >
          <v-select
            :readonly="$props.readonly"
            v-model="formFields.region_id"
            :items="regions"
            label="Regions"
            :disabled="loading"
            :loading="loading"
            :error-messages="errors"
            :success="valid"
            item-text="name"
            item-value="id"
          ></v-select>
        </ValidationProvider>
        <ValidationProvider
          rules="required"
          v-slot="{ errors, valid }"
          name="Countries"
        >
          <v-select
            :readonly="$props.readonly"
            v-model="formFields.country_id"
            :items="countries"
            label="Countries"
            :disabled="loading"
            :loading="loading"
            :error-messages="errors"
            :success="valid"
            item-text="name"
            item-value="iso2_code"
          ></v-select>
        </ValidationProvider>
        <ValidationProvider
          rules="required|max:191"
          v-slot="{ errors, valid }"
          name="Street"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.street"
            label="Street"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="max:191"
          v-slot="{ errors, valid }"
          name="Second Street"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.street1"
            label="Second Street"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
      </v-container>
      <v-container>
        <v-row v-if="!$props.readonly">
          <v-col cols="12" align="center" justify="center">
            <v-btn
              class="mr-4"
              type="submit"
              :loading="loading"
              :disabled="invalid || loading"
              color="secondary"
              >submit
            </v-btn>
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
      defaultValues: {},
      countries: [],
      regions: [],
      formFields: {
        name: null,
        street: null,
        street1: null,
        region_id: null,
        country_id: null
      }
    };
  },
  mounted() {
    this.getAllRegions();
    this.getAllCountries();
    this.defaultValues = { ...this.formFields };
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
    }
  },
  beforeDestroy() {
    this.$off("submit");
  },
  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;

      if (this.$props.model) {
        this.request({
          method: "patch",
          url: "store-pickups/update",
          data: { ...this.formFields }
        })
          .then(() => {
            this.$emit("submit", {
              action: "paginate"
            });
          })
          .finally(() => {
            this.loading = false;
          });
      } else {
        this.request({
          method: "post",
          url: "store-pickups/create",
          data: { ...this.formFields }
        })
          .then(() => {
            this.$emit("submit", {
              action: "paginate"
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
        url: "regions"
      })
        .then(response => {
          this.regions = response;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllCountries() {
      this.request({
        method: "get",
        url: "countries"
      }).then(response => {
        this.countries = response;
      });
    }
  }
};
</script>
