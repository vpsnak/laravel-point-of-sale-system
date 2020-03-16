<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        PERSONAL INFORMATION
        <ValidationProvider
          rules="required|max:255"
          v-slot="{ errors, valid }"
          name="First name"
        >
          <v-text-field
            v-model="firstName"
            label="First name"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|max:255"
          v-slot="{ errors, valid }"
          name="Last Name"
        >
          <v-text-field
            v-model="lastName"
            label="Last name"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|email|max:255"
          v-slot="{ errors, valid }"
          name="Email"
        >
          <v-text-field
            v-model="formFields.email"
            label="Email"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          :rules="{
            min: 8,
            max: 255,
            regex: /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g
          }"
          v-slot="{ errors, valid }"
          name="Phone"
        >
          <v-text-field
            v-model="phone"
            label="Phone"
            :min="0"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>

        <v-row justify="space-around">
          <ValidationProvider vid="house_account_status">
            <v-switch
              v-model="formFields.house_account_status"
              label="Has house account"
            ></v-switch>
          </ValidationProvider>
        </v-row>
        <v-row justify="space-around">
          <v-col v-if="formFields.house_account_status">
            <ValidationProvider
              rules="required_if:house_account_status|max:255"
              v-slot="{ errors, valid }"
              name="House account number"
            >
              <v-text-field
                v-model="formFields.house_account_number"
                label="House account number"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
              rules="required_if:house_account_status|max:8|numeric"
              v-slot="{ errors, valid }"
              name="House account limit"
            >
              <v-text-field
                type="number"
                v-model="formFields.house_account_limit"
                label="House account limit"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <ValidationProvider
          rules="max:65535"
          v-slot="{ errors, valid }"
          name="Comment"
        >
          <v-textarea
            rows="3"
            v-model="formFields.comment"
            label="Comments"
            :error-messages="errors"
            :success="valid"
          ></v-textarea>
        </ValidationProvider>

        <v-checkbox
          v-model="syncName"
          label="Use customer's name as default address name"
        ></v-checkbox
        >BILLING INFORMATION
        <v-row>
          <v-col cols="6">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors, valid }"
              name="First Name"
            >
              <v-text-field
                v-model="formFields.address.first_name"
                label="First name"
                :disabled="loading || syncName"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors, valid }"
              name="Address"
            >
              <v-text-field
                v-model="formFields.address.street"
                label="Address"
                :disabled="loading"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>

          <v-col cols="6">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors, valid }"
              name="Last Name"
            >
              <v-text-field
                v-model="formFields.address.last_name"
                label="Last name"
                :disabled="loading || syncName"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
              rules="max:100"
              v-slot="{ errors, valid }"
              name="Second Address"
            >
              <v-text-field
                v-model="formFields.address.street2"
                label="Second Address"
                :disabled="loading"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col cols="3">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors, valid }"
              name="City"
            >
              <v-text-field
                v-model="formFields.address.city"
                label="City"
                :disabled="loading"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col cols="3">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors, valid }"
              name="Zip code"
            >
              <v-text-field
                v-model="formFields.address.postcode"
                label="Zip code"
                :disabled="loading"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col cols="3">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors, valid }"
              name="State"
            >
              <v-select
                v-model="formFields.address.region_id"
                :items="regions"
                label="States"
                required
                item-text="name"
                item-value="id"
                :error-messages="errors"
                :success="valid"
              ></v-select>
            </ValidationProvider>
          </v-col>
          <v-col cols="3">
            <ValidationProvider
              rules="required|max:100"
              v-slot="{ errors, valid }"
              name="Countries"
            >
              <v-select
                v-model="formFields.address.country_id"
                :items="countries"
                label="Countries"
                required
                item-text="name"
                item-value="id"
                :error-messages="errors"
                :success="valid"
              ></v-select>
            </ValidationProvider>
          </v-col>
          <v-col cols="4">
            <ValidationProvider
              rules="max:100"
              v-slot="{ errors, valid }"
              name="Location"
            >
              <v-select
                v-model="formFields.location"
                label="Locations"
                :items="locations"
                item-text="label"
                item-value="label"
                :disabled="loading"
                :error-messages="errors"
                :success="valid"
              ></v-select>
            </ValidationProvider>
          </v-col>
          <v-col cols="4">
            <ValidationProvider
              rules="max:100"
              v-slot="{ errors, valid }"
              name="Location name"
            >
              <v-text-field
                v-model="formFields.address.location_name"
                label="Location name"
                :disabled="loading"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col cols="4">
            <ValidationProvider
              :rules="{
                min: 8,
                max: 100,
                regex: /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g
              }"
              v-slot="{ errors, valid }"
              name="Phone"
            >
              <v-text-field
                v-model="formFields.address.phone"
                label="Phone"
                :min="0"
                :disabled="loading || syncName"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
      </v-container>
      <v-container>
        <v-row>
          <v-col cols="12" align="center" justify="center">
            <v-btn
              color="primary"
              class="mr-4"
              type="submit"
              :loading="loading"
              :disabled="invalid || loading"
              >submit</v-btn
            >
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>
<script>
import { mapState, mapActions } from "vuex";

export default {
  data() {
    return {
      loading: false,
      syncName: true,
      countries: [],
      regions: [],
      defaultValues: {},
      formFields: {
        first_name: null,
        last_name: null,
        email: null,
        house_account_number: null,
        house_account_limit: null,
        house_account_status: false,
        no_tax: false,
        comment: null,
        phone: null,
        address: {
          first_name: null,
          last_name: null,
          street: null,
          street2: null,
          city: null,
          country_id: null,
          region_id: null,
          postcode: null,
          phone: null,
          is_default_billing: true,
          is_default_shipping: true,
          location: null,
          location_name: null
        }
      }
    };
  },
  mounted() {
    this.request({
      method: "get",
      url: "regions"
    }).then(response => {
      this.regions = response;
    });
    this.request({
      method: "get",
      url: "countries"
    }).then(response => {
      this.countries = response;
    });
    this.defaultValues = { ...this.formFields };
  },
  computed: {
    firstName: {
      get() {
        return this.formFields.first_name;
      },
      set(value) {
        if (this.syncName) {
          this.formFields.address.first_name = this.formFields.first_name = value;
        } else {
          this.formFields.first_name = value;
        }
      }
    },
    lastName: {
      get() {
        return this.formFields.last_name;
      },
      set(value) {
        if (this.syncName) {
          this.formFields.address.last_name = this.formFields.last_name = value;
        } else {
          this.formFields.last_name = value;
        }
      }
    },
    phone: {
      get() {
        return this.formFields.phone;
      },
      set(value) {
        if (this.syncName) {
          this.formFields.address.phone = this.formFields.phone = value;
        } else {
          this.formFields.phone = value;
        }
      }
    },
    locations() {
      return this.$store.state.cart.locations;
    }
  },
  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;
      let payload = {
        method: "post",
        url: "customers/create",
        data: { ...this.formFields }
      };

      this.request(payload)
        .then(response => {
          this.$emit("submit", {
            data: { customer: response },
            action: "paginate",
            notification: {
              msg: "Customer added successfully",
              type: "success"
            }
          });
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
