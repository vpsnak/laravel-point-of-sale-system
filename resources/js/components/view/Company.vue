<template>
  <v-container>
    <v-row v-if="companyData">
      <v-col cols="12">
        <v-card>
          <v-card-title>{{ companyData.name }}</v-card-title>
          <v-card-text>
            <div class="subtitle-1">
              Tax number: {{ companyData.tax_number }}
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
    model: Number
  },
  data() {
    return {
      company: null
    };
  },
  mounted() {
    if (this.model)
      this.getOne({
        model: "companies",
        data: {
          id: this.model.id
        }
      }).then(result => {
        this.company = result;
      });
  },
  computed: {
    companyData() {
      return this.company;
    }
  },
  methods: {
    ...mapActions({
      getOne: "getOne"
    })
  }
};
</script>
