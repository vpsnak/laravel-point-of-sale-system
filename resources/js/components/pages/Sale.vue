<template>
  <v-container fluid class="fill-height py-0">
    <v-row class="fill-height">
      <v-col :lg="7" :cols="12" class="pa-lg-0">
        <productList />
      </v-col>
      <v-col :lg="5" :cols="12" class="py-lg-0 pl-lg-0">
        <cart
          editable
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
