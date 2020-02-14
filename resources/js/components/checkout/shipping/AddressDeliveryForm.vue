<template>
  <ValidationObserver
    v-slot="{ invalid }"
    ref="addressDeliveryObs"
    tag="form"
    @submit.prevent="submit"
  >
    <div>
      <v-row>
        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="First name"
          >
            <v-text-field
              v-model="formFields.first_name"
              :readonly="$props.readonly"
              label="First name"
              :disabled="loading"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Address"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.street"
              label="Address"
              :disabled="loading"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>

        <v-col cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Last Name"
          >
            <v-text-field
              v-model="formFields.last_name"
              :readonly="$props.readonly"
              label="Last name"
              :disabled="loading"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>

          <ValidationProvider v-slot="{ errors, valid }" name="Second Address">
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.street2"
              label="Second Address"
              :disabled="loading"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="City"
          >
            <v-text-field
              v-model="formFields.city"
              :readonly="$props.readonly"
              label="City"
              :disabled="loading"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Zip Code"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.postcode"
              label="Zip Code"
              :disabled="loading"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="State"
          >
            <v-select
              v-model="formFields.region_id"
              :readonly="$props.readonly"
              :items="regions"
              label="States"
              item-text="default_name"
              item-value="region_id"
              :error-messages="errors"
              :success="valid"
            ></v-select>
          </ValidationProvider>
        </v-col>
        <v-col cols="3">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Country"
          >
            <v-select
              :readonly="$props.readonly"
              v-model="formFields.country_id"
              :items="countries"
              label="Countries"
              required
              item-text="iso2_code"
              item-value="iso2_code"
              :error-messages="errors"
              :success="valid"
            ></v-select>
          </ValidationProvider>
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Phone"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.phone"
              label="Phone"
              :disabled="loading"
              :error-messages="errors"
              :success="valid"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            rules="required"
            v-slot="{ errors, valid }"
            name="Location"
          >
            <v-select
              :readonly="$props.readonly"
              :error-messages="errors"
              :success="valid"
              :disabled="loading"
              label="Location"
              :items="locations"
              item-text="label"
              item-value="id"
              v-model="formFields.location"
              prepend-icon="mdi-city"
            ></v-select>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row v-if="!$props.readonly">
        <v-spacer />
        <v-btn type="submit" :disabled="invalid || loading" :loading="loading">
          Submit
        </v-btn>
      </v-row>
    </div>
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
      loading: false,
      countries: [],
      regions: [],
      default: {
        first_name: "",
        last_name: "",
        street: null,
        street2: null,
        city: null,
        country_id: null,
        region_id: null,
        postcode: null,
        phone: null,
        deliverydate: null,
        billing: false,
        location: 11
      }
    };
  },

  mounted() {
    this.getAllRegions();
    this.getAllCountries();
  },
  computed: {
    ...mapState("cart", ["locations", "customer"]),

    formFields: {
      get() {
        if (this.$props.model) {
          return this.$props.model;
        } else {
          return this.default;
        }
      }
    }
  },
  methods: {
    ...mapMutations(["setNotification"]),

    submit() {
      this.formFields.region = this.formFields.region_id;

      let payload = {
        model: "addresses",
        data: this.formFields
      };

      payload.data.customer_id = this.customer.id;

      this.create(payload).then(response => {
        this.setNotification({
          msg: response.info,
          type: "success"
        });

        this.$emit("submit", { data: response["address"] });
      });
    },

    getAllRegions() {
      this.loading = true;
      this.getAll({
        model: "regions"
      })
        .then(regions => {
          this.regions = regions;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllCountries() {
      this.getAll({
        model: "countries"
      }).then(countries => {
        this.countries = countries;
      });
    },
    ...mapActions(["create", "getAll"])
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
