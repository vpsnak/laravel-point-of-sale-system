<template>
  <v-card elevation="12">
    <v-tabs grow centered>
      <v-tab>
        <v-icon left>mdi-cash-multiple</v-icon>
        <span>Order summary & actions</span>
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
        <v-row no-gutters>
          <v-col :cols="6">
            <orderStatus />
            <orderDetails />
          </v-col>
          <v-col :cols="6">
            <orderCostAnalysis editOrder />
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col :md="6">
            <paymentHistory editOrder />
          </v-col>
          <v-col :md="6">
            <orderPaymentAnalysis />
          </v-col>
        </v-row>
        <v-row no-gutters>
          <paymentRefunds />
        </v-row>
      </v-tab-item>
      <v-tab-item>
        <v-card flat>
          <v-card-text>
            <p>items grid</p>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item>
        <v-card flat>
          <v-card-text>
            <p>delivery</p>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </v-card>
</template>
