<template>
  <v-tab-item>
    <v-row no-gutters justify="center">
      <v-col :md="6" :cols="12">
        <OrderInfo />
      </v-col>
      <v-col :md="3" :cols="12">
        <orderCostAnalysis />
      </v-col>
      <v-col :md="3" :cols="12">
        <orderIncomeAnalysis :orderId="Number($route.params.id)" />
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col :cols="12">
        <transactionHistory />
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col :cols="12">
        <orderActions />
      </v-col>
    </v-row>
  </v-tab-item>
</template>
