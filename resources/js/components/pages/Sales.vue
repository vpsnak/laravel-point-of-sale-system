<template>
  <v-container fluid>
    <v-row dense>
      <v-col :lg="7" :cols="12">
        <productList />
      </v-col>
      <v-col :lg="5" :cols="12">
        <cart :editable="true" :actions="true" :order_edit="order_edit" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  props: {
    order_edit: Boolean
  }
};
</script>
