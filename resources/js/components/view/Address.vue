<template>
  <v-container>
    <v-row v-if="addressData">
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ addressData.first_name }}
            {{ addressData.last_name }}
          </v-card-title>
          <v-card-text>
            <div class="subtitle-1">Street: {{ addressData.street }}</div>
            <div class="subtitle-1">Street 2: {{ addressData.street2 }}</div>
            <div class="subtitle-1">City: {{ addressData.city }}</div>
            <div class="subtitle-1">
              Country id: {{ addressData.region.country.name }}
            </div>
            <div class="subtitle-1">Region: {{ addressData.region.name }}</div>
            <div class="subtitle-1">Post code: {{ addressData.postcode }}</div>
            <div class="subtitle-1">Phone: {{ addressData.phone }}</div>
            <div class="subtitle-1">Company: {{ addressData.company }}</div>
            <div class="subtitle-1">Vat ID: {{ addressData.vat_id }}</div>
            <div class="subtitle-1">
              Delivery date: {{ addressData.delivery_date }}
            </div>
            <div class="subtitle-1">
              Default Billing: {{ addressData.billing ? "Yes" : "No" }}
            </div>
            <div class="subtitle-1">
              Default Shipping: {{ addressData.shipping ? "Yes" : "No" }}
            </div>
            <div class="subtitle-1">
              Created at: {{ addressData.created_at }}
            </div>
            <div class="subtitle-1">
              Updated at: {{ addressData.updated_at }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row v-else>
      <v-col cols="12" align="center" justify="center">
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Object
  },
  data() {
    return {
      address: null
    };
  },
  mounted() {
    if (this.$props.model)
      this.request({
        method: "get",
        url: `addresses/get/${this.$props.model.id}`
      }).then(result => {
        this.address = result;
      });
  },
  computed: {
    addressData() {
      return this.address;
    }
  },
  methods: {
    ...mapActions("requests", ["request"])
  }
};
</script>
