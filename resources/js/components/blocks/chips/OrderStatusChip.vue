<template>
  <v-menu
    v-model="statusesDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
    offset-y
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title" v-text="'Status'" />
      <v-chip
        dark
        label
        v-on="$props.menu ? on : null"
        :color="latestStatus.color"
        :small="small"
      >
        <v-icon left v-text="$props.latestStatus.icon" />
        <b v-text="$props.latestStatus.text" />
      </v-chip>
    </template>
    <v-card class="pa-5 fill-height" outlined width="750">
      <v-container fluid>
        <v-row v-if="loading" justify="center" align="center">
          <div class="text-center ma-12">
            <v-progress-circular
              :indeterminate="true"
              :size="100"
              :width="10"
              color="light-blue"
            />
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
            item-key="pivot.id"
          >
            <template v-slot:item.text="{ item }">
              <v-chip label :color="item.color">
                <v-icon left v-text="item.icon" />
                <span v-text="item.text" />
              </v-chip>
            </template>
            <template v-slot:item.created_at="{ item }">
              <timestampChip
                icon="mdi-calendar"
                :timestamp="item.pivot.created_at"
              />
            </template>
            <template v-slot:item.processed_by="{ item }">
              <userChip menu :user="item.pivot.processed_by" />
            </template>
          </v-data-table>
        </v-row>
      </v-container>
    </v-card>
  </v-menu>
</template>

<script>
import { mapActions } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    small: Boolean,
    menu: Boolean,
    orderId: Number,
    title: Boolean,
    latestStatus: Object
  },

  beforeDestroy() {
    EventBus.$off("overlay");
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
      EventBus.$emit("overlay", value);
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
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
