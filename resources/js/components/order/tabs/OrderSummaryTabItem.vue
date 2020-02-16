<template>
  <v-tab-item>
    <v-row no-gutters justify="center">
      <v-col :cols="6">
        <orderStatus />
      </v-col>
      <v-col :cols="6">
        <orderGraphs />
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col :cols="12">
        <paymentHistory editOrder />
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col :cols="12">
        <paymentRefunds />
      </v-col>
    </v-row>
  </v-tab-item>
</template>
