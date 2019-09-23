<template>
	<div>
		<v-list-item :key="payment.id" v-for="payment in payments">
			<v-list-item-content>
				<v-list-item-title @click="removePayment(payment)">{{ payment.type + ' - ' + payment.amount }}$
				</v-list-item-title>
			</v-list-item-content>
		</v-list-item>
		<v-row class="d-flex justify-space-between pa-2" no-gutters>
			<span>Total Paid</span>
			<span>$ {{totalPaid}}</span>
		</v-row>
		<v-divider />
		<v-row class="d-flex justify-space-between pa-2" no-gutters>
			<span>Total</span>
			<span>$ {{ total }}</span>
		</v-row>
		<v-divider />
		<v-row class="d-flex justify-space-between pa-2" no-gutters>
			<span>Remains</span>
			<span>$ {{ getRemainingAmount() }}</span>
		</v-row>
		<v-divider />
		<v-row no-gutters>
			<v-text-field
					label="Amount"
					prefix="$"
					type="number"
					v-model="payment_amount"
			/>
		</v-row>
		<v-row no-gutters>
			<v-btn :disabled="getRemainingAmount() <= 0"
				   @click="handlePaymentButton"
			>Cash
			</v-btn>
			<v-btn :disabled="getRemainingAmount() <= 0"
				   @click="handlePaymentButton"
			>Card
			</v-btn>
			<v-btn :disabled="getRemainingAmount() <= 0"
				   @click="handlePaymentButton"
			>Coupon
			</v-btn>
			<v-btn :disabled="getRemainingAmount() <= 0"
				   @click="handlePaymentButton"
			>GiftCard
			</v-btn>
			{{getPaymentAmount()}}
			<v-btn @click="setPayments([])">Clear</v-btn>
		</v-row>
	</div>
</template>
<script>
  import {mapActions, mapGetters, mapMutations, mapState} from 'vuex'

  export default {
    data() {
      return {
        remaining_amount: this.getRemainingAmount()
      }
    },
    props: ['totalAmount', 'payAmount'],
    mounted() {
      this.fetchPayments()
      this.setTotal(this.totalAmount)
      this.payment_amount = this.payAmount | this.totalAmount
    },
    computed: {
      payment_amount: {
        get() {
          return this.getRemainingAmount()
        },
        set(value) {
          console.log(value)
          return value
        }
      },
      ...mapState('payment', {
        payments: 'payments',
        total: 'total'
      }),
      ...mapGetters('payment', {
        totalPaid: 'getTotalPaid'
      })
    },
    methods: {
      getRemainingAmount() {
        return this.total - this.totalPaid
      },
      getPaymentAmount() {
        this.payment_amount = this.getRemainingAmount()
      },
      handlePaymentAmountInput(value) {
        console.log(value)
        this.payment_amount = this.getPaymentAmount(value)
      },
      handlePaymentButton(event) {
        this.addPayment({id: event.timeStamp, type: event.target.innerText, amount: this.payment_amount})
      },
      ...mapActions('payment', {
        fetchPayments: 'fetchPayments',
        removePayment: 'removePayment',
        addPayment: 'addPayment'
      }),
      ...mapMutations('payment', {
        setTotal: 'setTotal',
        setPayments: 'setPayments'
      })
    }
  }
</script>