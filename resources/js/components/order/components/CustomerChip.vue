<template>
  <v-menu
    v-model="customerDetails"
    bottom
    right
    transition="scale-transition"
    :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <h5 v-if="$props.title">Customer</h5>
      <v-chip
        pill
        v-on="$props.customer && $props.menu ? on : null"
        :color="$props.customer ? 'primary' : null"
      >
        <v-icon left>mdi-account-outline</v-icon>
        {{ $props.customer ? $props.customer.full_name : "Guest" }}
      </v-chip>
    </template>
    <v-card width="450" class="pa-5" outlined>
      <customerForm :model="$props.customer" :readonly="true" />
    </v-card>
  </v-menu>
</template>

<script>
export default {
  props: {
    title: Boolean,
    menu: Boolean,
    customer: Object
  },

  data() {
    return {
      customerDetails: false
    };
  }
};
</script>
