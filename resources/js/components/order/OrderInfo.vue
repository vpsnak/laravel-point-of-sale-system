<template>
  <v-card outlined class="fill-height">
    <v-card-title>
      <v-icon left>mdi-information-variant</v-icon>
      <span class="subheading">
        Info
      </span>
    </v-card-title>
    <v-container>
      <v-row justify="center" align="center">
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <userChip title menu :user="order_created_by" />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <timestampChip
            title="Created"
            icon="mdi-calendar-plus"
            :timestamp="order_timestamp.created_at"
          />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <timestampChip
            title="Last update"
            icon="mdi-calendar-edit"
            :timestamp="order_timestamp.updated_at"
          />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <storeChip :title="true" menu :store="order_store" />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <orderStatusChip
            menu
            :title="true"
            :latestStatus="order_status"
            :orderId="order_id"
          />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <masStatusChip :title="true" :mas_order="order_mas_order" />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <orderMethodChip :method="method" menu :title="true" />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <customerChip :title="true" menu :customer="customer" />
        </v-col>
        <v-col :lg="4" :md="6" :cols="12" class="text-center">
          <v-menu
            v-model="noteDetails"
            bottom
            right
            transition="scale-transition"
            :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <h5>Notes</h5>
              <v-chip
                label
                v-on="on"
                :color="order_notes ? 'orange' : null"
                :disabled="!order_notes"
              >
                <v-icon>{{
                  order_notes
                    ? "mdi-message-bulleted"
                    : "mdi-message-bulleted-off"
                }}</v-icon>
              </v-chip>
            </template>
            <v-card width="450" class="pa-5" outlined>
              <v-container fluid>
                <v-row>
                  <orderNotes :readonly="true" />
                </v-row>
              </v-container>
            </v-card>
          </v-menu>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>
<script>
import { mapState } from "vuex";

export default {
  data() {
    return {
      noteDetails: false
    };
  },

  computed: {
    ...mapState("cart", [
      "order_id",
      "order_store",
      "order_created_by",
      "order_timestamp",
      "order_status",
      "order_mas_order",
      "order_delivery_store_pickup",
      "order_delivery_address",
      "order_notes",
      "method",
      "customer"
    ])
  }
};
</script>
