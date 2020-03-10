<template>
  <v-container>
    <v-row v-if="giftCardData">
      <v-col cols="12">
        <v-card>
          <v-card-title>{{ giftCardData.name }}</v-card-title>
          <v-card-text>
            <div class="subtitle-1">Code: {{ giftCardData.code }}</div>
            <div class="subtitle-1">
              Enabled: {{ giftCardData.is_enabled ? "Yes" : "No" }}
            </div>
            <div class="subtitle-1">Amount: {{ giftCardData.amount }} $</div>
            <div class="subtitle-1">
              Created at: {{ giftCardData.created_at }}
            </div>
            <div class="subtitle-1">
              Updated at: {{ giftCardData.updated_at }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row v-else>
      <v-col cols="12" align="center" justify="center">
        <v-progress-circular
          indeterminate
          color="secondary"
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
      giftCard: null
    };
  },
  mounted() {
    if (this.$props.model)
      this.request({
        method: "get",
        url: `gift-cards/get/${this.$props.model.id}`
      }).then(result => {
        this.giftCard = result;
      });
  },
  computed: {
    giftCardData() {
      return this.giftCard;
    }
  },
  methods: {
    ...mapActions("requests", ["request"])
  }
};
</script>
