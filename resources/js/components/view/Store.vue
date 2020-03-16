<template>
  <v-container>
    <v-row v-if="storeData">
      <v-col cols="12">
        <v-card>
          <v-card-title>{{ storeData.name }}</v-card-title>
          <v-card-text>
            <div class="subtitle-1">Tax: {{ storeData.tax.name }}</div>
            <div class="subtitle-1">Created by: {{ user.name }}</div>
            <div class="subtitle-1">Phone: {{ storeData.phone }}</div>
            <div class="subtitle-1">Street: {{ storeData.street }}</div>
            <div class="subtitle-1">Postcode: {{ storeData.postcode }}</div>
            <div class="subtitle-1">City: {{ storeData.city }}</div>
            <div class="subtitle-1">Created at: {{ storeData.created_at }}</div>
            <div class="subtitle-1">Updated at: {{ storeData.updated_at }}</div>
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
      store: null,
      user: ""
    };
  },
  mounted() {
    if (this.$props.model)
      this.request({
        method: "get",
        url: `stores/get/${this.$props.model.id}`
      }).then(result => {
        this.store = result;
        this.request({
          method: "get",
          url: `users/get/${this.storeData.user_id}`
        }).then(response => {
          this.user = response;
        });
      });
  },
  computed: {
    storeData() {
      return this.store;
    }
  },
  methods: {
    ...mapActions("requests", ["request"])
  }
};
</script>
