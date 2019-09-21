<template>
  <v-card ref="form">
    <div class="text-center">
      <v-chip class="mb-n8 mx-auto" elevation="5" color="primary" label>
        <v-icon left>fas fa-user-astronaut</v-icon>Customer Form
      </v-chip>
    </div>
    <v-card-text>
      <v-text-field
        ref="firstname"
        v-model="firstname"
        :rules="[() => !!name || 'This field is required']"
        :error-messages="errorMessages"
        label="First name"
        placeholder="Panos"
        required
      ></v-text-field>
      <v-text-field
        ref="lastname"
        v-model="lastname"
        :rules="[() => !!name || 'This field is required']"
        :error-messages="errorMessages"
        label="Last name"
        placeholder="Meletis"
        required
      ></v-text-field>
      <v-text-field
        ref="email"
        v-model="email"
        :rules="[ v => !!v || 'E-mail is required',
        v => /.+@.+/.test(v) || 'E-mail must be valid']"
        :error-messages="errorMessages"
        label="Email"
        placeholder="Email"
        required
      ></v-text-field>
      <v-text-field
        ref="company_name"
        v-model="company_name"
        :rules="[
              () => !!company_name || 'This field is required',
              () => !!company_name && company_name.length <= 25 || 'Company name must be less than 25 characters',
               lengthCheck
            ]"
        label="Company name"
        placeholder="Web O2"
        counter="25"
        required
      ></v-text-field>
      <v-autocomplete
        ref="country"
        v-model="country"
        :rules="[() => !!country || 'This field is required']"
        :items="countries"
        label="Country"
        placeholder="Select..."
        required
      ></v-autocomplete>
    </v-card-text>
    <v-divider class="mt-12"></v-divider>
    <v-card-actions>
      <v-btn text>Cancel</v-btn>
      <div class="flex-grow-1"></div>
      <v-slide-x-reverse-transition>
        <v-tooltip v-if="formHasErrors" left>
          <template v-slot:activator="{ on }">
            <v-btn icon class="my-0" @click="resetForm" v-on="on">
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
          </template>
          <span>Refresh form</span>
        </v-tooltip>
      </v-slide-x-reverse-transition>
      <v-btn color="primary" text @click="submit">Submit</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      countries: [
        "Afghanistan",
        "Albania",
        "Algeria",
        "Andorra",
        "Angola",
        "Anguilla",
        "Antigua &amp; Barbuda",
        "Argentina",
        "Armenia",
        "Aruba",
        "Australia",
        "Austria",
        "Azerbaijan",
        "Bahamas",
        "Bahrain",
        "Bangladesh",
        "Barbados",
        "Belarus",
        "Belgium",
        "Belize",
        "Benin",
        "Bermuda",
        "Bhutan",
        "Bolivia",
        "Bosnia &amp; Herzegovina",
        "Botswana",
        "Brazil",
        "British Virgin Islands",
        "Brunei",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Cambodia",
        "Cameroon",
        "Cape Verde",
        "Cayman Islands",
        "Chad",
        "Chile",
        "China",
        "Colombia",
        "Congo",
        "Cook Islands",
        "Costa Rica",
        "Cote D Ivoire",
        "Croatia",
        "Cruise Ship",
        "Cuba",
        "Cyprus",
        "Czech Republic",
        "Denmark",
        "Djibouti",
        "Dominica",
        "Dominican Republic",
        "Ecuador",
        "Egypt",
        "El Salvador",
        "Equatorial Guinea",
        "Estonia",
        "Ethiopia",
        "Falkland Islands",
        "Faroe Islands",
        "Fiji",
        "Finland",
        "France",
        "French Polynesia",
        "French West Indies",
        "Gabon",
        "Gambia",
        "Georgia",
        "Germany",
        "Ghana",
        "Gibraltar",
        "Greece",
        "Greenland",
        "Grenada",
        "Guam",
        "Guatemala",
        "Guernsey",
        "Guinea",
        "Guinea Bissau",
        "Guyana",
        "Haiti",
        "Honduras",
        "Hong Kong",
        "Hungary",
        "Iceland",
        "India",
        "Indonesia",
        "Iran",
        "Iraq",
        "Ireland",
        "Isle of Man",
        "Israel",
        "Italy",
        "Jamaica",
        "Japan",
        "Jersey",
        "Jordan",
        "Kazakhstan",
        "Kenya",
        "Kuwait",
        "Kyrgyz Republic",
        "Laos",
        "Latvia",
        "Lebanon",
        "Lesotho",
        "Liberia",
        "Libya",
        "Liechtenstein",
        "Lithuania",
        "Luxembourg",
        "Macau",
        "Macedonia",
        "Madagascar",
        "Malawi",
        "Malaysia",
        "Maldives",
        "Mali",
        "Malta",
        "Mauritania",
        "Mauritius",
        "Mexico",
        "Moldova",
        "Monaco",
        "Mongolia",
        "Montenegro",
        "Montserrat",
        "Morocco",
        "Mozambique",
        "Namibia",
        "Nepal",
        "Netherlands",
        "Netherlands Antilles",
        "New Caledonia",
        "New Zealand",
        "Nicaragua",
        "Niger",
        "Nigeria",
        "Norway",
        "Oman",
        "Pakistan",
        "Palestine",
        "Panama",
        "Papua New Guinea",
        "Paraguay",
        "Peru",
        "Philippines",
        "Poland",
        "Portugal",
        "Puerto Rico",
        "Qatar",
        "Reunion",
        "Romania",
        "Russia",
        "Rwanda",
        "Saint Pierre &amp; Miquelon",
        "Samoa",
        "San Marino",
        "Satellite",
        "Saudi Arabia",
        "Senegal",
        "Serbia",
        "Seychelles",
        "Sierra Leone",
        "Singapore",
        "Slovakia",
        "Slovenia",
        "South Africa",
        "South Korea",
        "Spain",
        "Sri Lanka",
        "St Kitts &amp; Nevis",
        "St Lucia",
        "St Vincent",
        "St. Lucia",
        "Sudan",
        "Suriname",
        "Swaziland",
        "Sweden",
        "Switzerland",
        "Syria",
        "Taiwan",
        "Tajikistan",
        "Tanzania",
        "Thailand",
        "Timor L'Este",
        "Togo",
        "Tonga",
        "Trinidad &amp; Tobago",
        "Tunisia",
        "Turkey",
        "Turkmenistan",
        "Turks &amp; Caicos",
        "Uganda",
        "Ukraine",
        "United Arab Emirates",
        "United Kingdom",
        "United States",
        "Uruguay",
        "Uzbekistan",
        "Venezuela",
        "Vietnam",
        "Virgin Islands (US)",
        "Yemen",
        "Zambia",
        "Zimbabwe"
      ],

      errorMessages: "",
      firstname: null,
      lastname: null,
      company_name: null,
      formHasErrors: false
    };
  },

  computed: {
    form() {
      return {
        firstname: this.firstname,
        lastname: this.lastname,
        company_name: this.company_name,
        country: this.country
      };
    }
  },

  watch: {
    name() {
      this.errorMessages = "";
    }
  },

  methods: {
    lengthCheck() {
      this.errorMessages =
        this.company_name && !this.firstname
          ? "Hey! I'm required (den to phre)"
          : "";

      return true;
    },
    resetForm() {
      this.errorMessages = [];
      this.formHasErrors = false;

      Object.keys(this.form).forEach(f => {
        this.$refs[f].reset();
      });
    },
    submit() {
      this.formHasErrors = false;

      Object.keys(this.form).forEach(f => {
        if (!this.form[f]) this.formHasErrors = true;

        this.$refs[f].validate(true);
      });
    }
  }
};
</script>