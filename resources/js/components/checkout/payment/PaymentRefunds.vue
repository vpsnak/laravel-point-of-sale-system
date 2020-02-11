<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>mdi-credit-card-refund-outline</v-icon>
      <span class="subheading">
        Make a refunds
      </span>
    </v-card-title>
    <v-form @submit="submit">
      <v-container>
        <v-row>
          <v-col>
            <v-text-field
              v-model="amount"
              label="Amount"
              required
            ></v-text-field>
            <v-btn color="success" type="submit">
              Refund
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      amount: null
    };
  }, methods: {
      submit(){

      }
  }
};
</script>
