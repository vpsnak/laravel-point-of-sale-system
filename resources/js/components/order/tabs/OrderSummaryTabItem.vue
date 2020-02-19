<template>
  <v-tab-item>
    <v-row no-gutters justify="center">
      <v-col :md="6">
        <orderStatus />
      </v-col>
      <v-col :md="3">
        <orderCostAnalysis />
      </v-col>
      <v-col :md="3">
        <orderPaymentAnalysis />
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col :cols="12">
        <paymentHistory editOrder />
      </v-col>
    </v-row>
    <v-row no-gutters v-if="this.$props.editable">
      <v-col :md="4">
        <orderActions />
      </v-col>
      <v-col :md="8">
        <paymentRefunds />
      </v-col>
    </v-row>
  </v-tab-item>
</template>

<script>
export default {
  props: {
    editable: Boolean
  }
};
</script>
