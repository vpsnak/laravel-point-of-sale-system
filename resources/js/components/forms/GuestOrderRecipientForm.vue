<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-container>
      <v-row>
        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient First name"
          >
            <v-text-field
              v-model="Recipient.RecipientFirstName"
              :readonly="$props.readonly"
              label="Recipient First name"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Address"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.RecipientAddress"
              label="Recipient Address"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Last Name"
          >
            <v-text-field
              v-model="Recipient.RecipientLastName"
              :readonly="$props.readonly"
              label="Recipient Last name"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>

          <ValidationProvider
            v-slot="{ errors }"
            name="Recipient Second Address"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.RecipientAddress2"
              label="Recipient Second Address"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Attention"
          >
            <v-text-field
              v-model="Recipient.RecipientAttention"
              :readonly="$props.readonly"
              label="Recipient Attention"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
          <ValidationProvider
            rules="required|email"
            v-slot="{ errors }"
            name="Recipient Email"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.RecipientEmail"
              label="Recipient Email"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient City"
          >
            <v-text-field
              v-model="Recipient.RecipientCity"
              :readonly="$props.readonly"
              label="Recipient City"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Zip Code"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.RecipientZip"
              label="Recipient Zip Code"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Country"
          >
            <v-autocomplete
              v-model="Recipient.country"
              @change="countryChanged"
              :readonly="$props.readonly"
              :items="countries"
              label="Recipient Country"
              required
              item-text="name"
              return-object
              :error-messages="errors"
              :loading="country_loading"
            />
          </ValidationProvider>

          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Phone"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.RecipientPhone"
              label="Recipient Phone"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient State"
          >
            <v-autocomplete
              v-model="Recipient.region"
              :loading="region_loading"
              :readonly="$props.readonly"
              :items="regions"
              label="Recipient State"
              item-text="name"
              return-object
              :error-messages="errors"
            ></v-autocomplete>
          </ValidationProvider>
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Special Instructions"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.SpecialInstructions"
              label="Recipient Special Instructions"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Occasion Type"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.OccasionType"
              label="Occasion Type"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Recipient Residence Type"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="Recipient.ResidenceType"
              label="Recipient Residence Type"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row v-if="!$props.readonly" justify="center">
        <v-btn
          color="primary"
          type="submit"
          :disabled="invalid || loading"
          :loading="submit_loading"
          >save
        </v-btn>
      </v-row>
    </v-container>
  </ValidationObserver>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";

export default {
  props: {
    model: Object,
    readonly: Boolean
  },

  data() {
    return {
      submit_loading: false,
      country_loading: false,
      region_loading: false,

      regions: [],
      countries: [],

      Recipient: {
        id: null,
        RecipientFirstName: null,
        RecipientLastName: null,
        RecipientAttention: null,
        RecipientEmail: null,
        RecipientAddress: null,
        RecipientAddress2: null,
        RecipientCity: null,
        country: null,
        region: null,
        RecipientZip: null,
        RecipientPhone: null,
        SpecialInstructions: null,
        OccasionType: null,
        ResidenceType: null
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

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    ...mapState("cart", ["locations", "customer"]),

    loading() {
      if (this.submit_loading || this.country_loading || this.region_loading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapMutations(["setNotification"]),
    ...mapActions("requests", ["request"]),

    countryChanged(country) {
      if (
        country &&
        this.Recipient.region &&
        this.Recipient.region.country.id !== country.id
      ) {
        this.Recipient.region = null;
      }
      this.getRegionsByCountry(country);
    },
    submit() {
      // TODO
      this.submit_loading = true;
      console.log(this.Recipient);
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
          }
        })
        .catch(error => {
          console.error(error);
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
