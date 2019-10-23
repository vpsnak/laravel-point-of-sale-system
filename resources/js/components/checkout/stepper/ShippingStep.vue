<template>
	<div>
		<shipping @shipping="setShipping" />
		<v-card-actions>
			<span class="title" v-if="shipping.cost">Shipping cost: $ {{ shipping.cost }}</span>
			<div class="flex-grow-1"></div>
            <div v-for="slot in timeslots">{{slot.label}}</div>
			<v-btn color="primary" v-if="showNext" @click="completeStep">
				Next
				<v-icon small right>mdi-chevron-right</v-icon>
			</v-btn>
		</v-card-actions>
	</div>
</template>

<script>
  import { mapActions } from "vuex";
export default {
	data() {
		return {
			shipping: {
				get() {
					return this.$store.state.cart.shipping;
				},
				set(value) {
					this.$store.state.cart.shipping = value;
				}
			},
          timeslots:[]
		};
	},
	props: {
		currentStep: Object
	},
  mounted(){
    this.postRequest({
      url:'shipping/timeslot',
      data:{
        postcode: '00601',
        date:'asd'
      }
    }).then(res => {
      this.timeslots = res
    })
  },

	computed: {
		showNext() {
			switch (this.shipping.method) {
				case undefined:
				case "retail":
					return true;
				case "pickup":
					if (this.shipping.date && this.shipping.time) {
						return true;
					} else {
						return false;
					}
				case "delivery":
					if (
						this.shipping.date &&
						this.shipping.time &&
						this.shipping.address
					) {
						return true;
					} else {
						return false;
					}
				default:
					return false;
			}
		}
	},

	methods: {
		completeStep() {
			this.$store.dispatch("cart/completeStep");
		},
		prevStep() {
			this.$store.state.cart.currentCheckoutStep--;
		},
		setShipping(value) {
			this.shipping = value;
		},
      ...mapActions(["postRequest"])
	}
};
</script>
