<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-row>
      <v-col :cols="6">
        <ValidationProvider
          rules="required"
          v-slot="{ errors }"
          name="First name"
        >
          <v-text-field
            v-model="address.first_name"
            :readonly="$props.readonly"
            label="First name"
            :disabled="loading"
            :error-messages="errors"
          ></v-text-field>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider
          rules="required"
          v-slot="{ errors }"
          name="Last Name"
        >
          <v-text-field
            v-model="address.last_name"
            :readonly="$props.readonly"
            label="Last name"
            :disabled="loading"
            :error-messages="errors"
          ></v-text-field>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider rules="required" v-slot="{ errors }" name="Address">
          <v-text-field
            :readonly="$props.readonly"
            v-model="address.street"
            label="Address"
            :disabled="loading"
            :error-messages="errors"
          ></v-text-field>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <v-text-field
          :readonly="$props.readonly"
          v-model="address.street2"
          label="Second Address"
          :disabled="loading"
        ></v-text-field>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider rules="required" v-slot="{ errors }" name="City">
          <v-text-field
            v-model="address.city"
            :readonly="$props.readonly"
            label="City"
            :disabled="loading"
            :error-messages="errors"
          ></v-text-field>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider
          rules="required|digits:5"
          v-slot="{ errors }"
          name="Zip Code"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="address.postcode"
            label="Zip Code"
            :disabled="loading"
            :error-messages="errors"
            type="number"
          ></v-text-field>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider rules="required" v-slot="{ errors }" name="Country">
          <v-autocomplete
            @change="countryChanged"
            :readonly="$props.readonly"
            v-model="address.country"
            :items="countries"
            label="Country"
            required
            item-text="name"
            return-object
            :error-messages="errors"
            :loading="country_loading"
          ></v-autocomplete>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider rules="required" v-slot="{ errors }" name="State">
          <v-autocomplete
            v-model="address.region"
            :loading="region_loading"
            :readonly="$props.readonly"
            :items="regions"
            label="State"
            item-text="name"
            return-object
            :error-messages="errors"
          ></v-autocomplete>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <ValidationProvider rules="required" v-slot="{ errors }" name="Phone">
          <v-text-field
            :readonly="$props.readonly"
            v-model="address.phone"
            label="Phone"
            :disabled="loading"
            :error-messages="errors"
          ></v-text-field>
        </ValidationProvider>
      </v-col>

      <v-col :cols="6">
        <v-select
          :readonly="$props.readonly"
          :disabled="loading"
          label="Location"
          :items="locations"
          item-text="label"
          return-object
          v-model="location"
          prepend-inner-icon="mdi-city"
        ></v-select>
      </v-col>
    </v-row>
    <v-row v-if="!$props.readonly" justify="center">
      <v-btn
        text
        color="primary"
        type="submit"
        :disabled="invalid || loading"
        :loading="submit_loading"
      >
        {{ submitBtnTxt }}
      </v-btn>
    </v-row>
  </ValidationObserver>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  props: {
    model: Object,
    readonly: Boolean
  },

  beforeDestroy() {
    this.$off("submit");
  },

  data() {
    return {
      submit_loading: false,
      country_loading: false,
      region_loading: false,

      regions: [],
      countries: [],
      selected_location: null,
      address: {
        id: null,
        customer_id: null,
        first_name: "",
        last_name: "",
        street: null,
        street2: null,
        city: null,
        region: null,
        country: null,
        postcode: null,
        phone: null,
        deliverydate: null,
        billing: false,
        location: 11
      }
    };
  },

  mounted() {
    if (this.$props.model) {
      this.getAllCountries(true);
      this.address = { ...this.address, ...this.$props.model };

      this.selected_location = _.find(this.locations, {
        id: this.$props.model.location
      });
    } else {
      this.getAllCountries(false);
    }
  },

  computed: {
    ...mapState("cart", ["locations", "customer"]),

    submitBtnTxt() {
      if (_.has(this.$props.model, "id")) {
        return "Save";
      } else {
        return "Create";
      }
    },
    location: {
      get() {
        return this.selected_location;
      },
      set(value) {
        this.selected_location = value;
        if (value) {
          this.address.location = value.id;
        } else {
          this.address.location = null;
        }
      }
    },
    loading() {
      if (this.submit_loading || this.country_loading || this.region_loading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    countryChanged(country) {
      if (
        country &&
        this.address.region &&
        this.address.region.country.id !== country.id
      ) {
        this.address.region = null;
      }
      this.getRegionsByCountry(country);
    },
    submit() {
      this.submit_loading = true;

      let payload = {
        method: "post",
        url: this.address.id ? "addresses/update" : "addresses/create",
        data: this.address
      };

      payload.data.customer_id = this.address.customer_id || this.customer.id;
      payload.data.region_id = this.address.region.id;
      payload.data.country_id = this.address.country.id;

      this.request(payload)
        .then(response => {
          this.$emit("submit", { data: response.address });
        })
        .finally(() => {
          this.submit_loading = false;
        });
    },
    getAllCountries(modelInit) {
      this.country_loading = true;

      const payload = {
        method: "get",
        url: "countries"
      };
      this.request(payload)
        .then(response => {
          this.countries = response;

          if (modelInit) {
            this.getRegionsByCountry(this.$props.model.region.country);
          }
          if (!this.address.country) {
            this.address.country = _.find(this.countries, {
              iso3_code: "USA"
            });

            this.regions = this.address.country.regions;
          }
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          this.country_loading = false;
        });
    },
    getRegionsByCountry(country) {
      this.region_loading = true;

      const payload = {
        method: "get",
        url: `countries/${country.id}/regions`
      };
      this.request(payload)
        .then(response => {
          this.regions = response;
        })
        .finally(() => {
          this.region_loading = false;
        });
    }
  }
};
</script>