<template>
  <v-container>
    <v-row v-if="categoryData">
      <v-col cols="12">
        <v-card>
          <v-card-title>{{ categoryData.name }}</v-card-title>
          <v-card-text>
            <div class="subtitle-1">
              Created at: {{ categoryData.created_at }}
            </div>
            <div class="subtitle-1">
              Updated at: {{ categoryData.updated_at }}
            </div>
            <div class="subtitle-1">
              In product listing:
              {{ categoryData.in_product_listing ? "Yes" : "No" }}
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
      category: null
    };
  },
  mounted() {
    if (this.model)
      this.getOne({
        model: "categories",
        data: {
          id: this.model.id
        }
      }).then(result => {
        this.category = result;
      });
  },
  computed: {
    categoryData() {
      return this.category;
    }
  },
  methods: {
    ...mapActions({
      getOne: "getOne"
    })
  }
};
</script>
