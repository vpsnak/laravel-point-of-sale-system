<template>
  <v-card outlined>
    <v-card-title>
      <v-icon left>mdi-chart-pie</v-icon>
      <span class="subheading">
        Cost & Payments Analysis
      </span>
    </v-card-title>
    <v-container>
      <v-row align="center" justify="space-around" no-gutters>
        <v-col>
          <orderCostAnalysis />
        </v-col>
        <v-col>
          <orderPaymentAnalysis />
        </v-col>
      </v-row>
    </v-container>
  </v-card>
</template>
