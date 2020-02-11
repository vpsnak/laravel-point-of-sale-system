<template>
  <v-card elevation="12">
    <v-tabs grow centered>
      <v-tab>
        <v-icon left>mdi-cash-multiple</v-icon>
        <span>Payments & Order status</span>
      </v-tab>
      <v-tab>
        <v-icon left>mdi-package-variant</v-icon>
        <span>Items</span>
      </v-tab>
      <v-tab>
        <v-icon left>mdi-file-document-edit-outline</v-icon>
        <span>Order Options</span>
      </v-tab>

      <v-tab-item>
        <v-card flat>
          <v-card-text>
            <paymentHistory editOrder />
            <paymentRefunds />
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item>
        <v-card flat>
          <v-card-text>
            <p>
              items grid
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item>
        <v-card flat>
          <v-card-text>
            <p>
              delivery
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </v-card>
</template>

<script>
export default {};
</script>
