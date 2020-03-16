<template>
  <v-container>
    <v-row v-if="storePickupData">
      <v-col cols="12">
        <v-card>
          <v-card-title>{{ storePickupData.name }}</v-card-title>
          <v-card-text>
            <div class="subtitle-1">Street: {{ storePickupData.street }}</div>
            <div class="subtitle-1">
              Street 2: {{ storePickupData.street1 }}
            </div>
            <div class="subtitle-1">
              Country id: {{ storePickupData.country_id }}
            </div>
            <div class="subtitle-1">
              Created at: {{ storePickupData.created_at }}
            </div>
            <div class="subtitle-1">
              Updated at: {{ storePickupData.updated_at }}
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
    model: Number
  },
  data() {
    return {
      storePickup: null
    };
  },
  mounted() {
    if (this.$props.model)
      this.request({
        method: "get",
        url: `store-pickups/get/${this.$props.model.id}`
      }).then(result => {
        this.storePickup = result;
      });
  },
  computed: {
    storePickupData() {
      return this.storePickup;
    }
  },
  methods: {
    ...mapActions("requests", ["request"])
  }
};
</script>
