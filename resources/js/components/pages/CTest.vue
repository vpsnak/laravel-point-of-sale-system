<template>
    <v-container fluid>
        <v-row>
            <v-col>
                <testTable></testTable>
            </v-col>
        </v-row>
        
        <v-list-item v-for="payment in payments" :key="payment.id">
          <v-list-item-content>
            <v-list-item-title @click="deletePayment(payment)">{{ payment.name + ' - ' + payment.amount }}$</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        {{totalPaid}}
        {{total}}
    </v-container>
</template>

<script>
import { mapGetters, mapState } from 'vuex'
export default {
  computed: {
    ...mapState({
      checkoutStatus: state => state.cart.checkoutStatus
    }),
    ...mapGetters('payment', {
      payments: 'paymentEntries',
      total: 'getTotal',
      totalPaid: 'getTotalPaid'
    })
  },
  methods: {
    deletePayment (payment) {
        console.log('removePayment method');
      this.$store.dispatch('payment/removePayment', payment)
    }
  }
};
</script>