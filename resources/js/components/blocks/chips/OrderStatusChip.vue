<template>
  <v-menu
    v-model="statusesDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title">Status</h5>
      <v-chip pill v-on="$props.menu ? on : null" :color="latestStatus.color">
        <v-icon left>{{ $props.latestStatus.icon }}</v-icon>
        {{ $props.latestStatus.text }}
      </v-chip>
    </template>
    <v-card class="pa-5 fill-height" outlined width="750" shaped>
      <v-container fluid>
        <v-row v-if="loading" justify="center" align="center">
          <div class="text-center ma-12">
            <v-progress-circular
              :indeterminate="true"
              :size="100"
              :width="10"
              color="light-blue"
            ></v-progress-circular>
          </div>
        </v-row>
        <v-row v-else justify="center" align="center" no-gutters>
          <v-data-table
            dense
            disable-filtering
            disable-pagination
            disable-sort
            fixed-header
            hide-default-footer
            :headers="headers"
            :items="statuses"
          >
            <template v-slot:item.text="{ item }">
              <v-chip pill :color="item.color">
                <v-icon left>{{ item.icon }}</v-icon>
                {{ item.text }}
              </v-chip>
            </template>
            <template v-slot:item.created_at="{ item }">
              <timestampChip
                icon="mdi-calendar"
                :timestamp="item.pivot.created_at"
              ></timestampChip>
            </template>
            <template v-slot:item.processed_by="{ item }">
              <createdByChip
                :menu="true"
                :created_by="item.pivot.processed_by"
              ></createdByChip>
            </template>
          </v-data-table>
        </v-row>
      </v-container>
    </v-card>
  </v-menu>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    menu: Boolean,
    orderId: Number,
    title: Boolean,
    latestStatus: Object
  },

  data() {
    return {
      loading: false,
      statusesDetails: false,
      statuses: [],
      headers: [
        { text: "Status", value: "text" },
        { text: "Processed on", value: "created_at" },
        { text: "Processed by", value: "processed_by" }
      ]
    };
  },

  watch: {
    statusesDetails(value) {
      if (value && !this.statuses.length) {
        this.getOrderStatuses();
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    getOrderStatuses() {
      this.loading = true;
      const payload = {
        method: "get",
        url: `orders/${this.$props.orderId}/statuses`
      };

      this.request(payload)
        .then(response => {
          this.statuses = response;
        })
        .catch()
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
