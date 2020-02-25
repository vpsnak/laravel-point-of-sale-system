<template>
  <v-container fluid>
    <v-row dense>
      <v-col :lg="7" :cols="12">
        <productList />
      </v-col>
      <v-col :lg="5" :cols="12">
        <cart
          :editable="true"
          :showMethods="$props.showMethods"
          :showCustomer="$props.showCustomer"
          :showActions="$props.showActions"
          :showSave="$props.showSave"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  props: {
    showMethods: Boolean,
    showCustomer: Boolean,
    showActions: Boolean,
    showSave: Boolean
  }
};
</script>
