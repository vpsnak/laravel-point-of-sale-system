<template>
  <v-container>
    <ValidationObserver ref="inStorePickupValidation">
      <v-form>
        <v-row align="center" justify="space-between">
          <v-col :lg="4" :md="6" :cols="12">
            <v-menu
              v-model="datePicker"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
              <template v-slot:activator="{ on }">
                <ValidationProvider
                  rules="required"
                  v-slot="{ errors, valid }"
                  name="Date"
                >
                  <v-text-field
                    readonly
                    v-model="dateFormatted"
                    label="Date"
                    prepend-inner-icon="event"
                    :error-messages="errors"
                    :success="valid"
                    v-on="$props.readonly ? null : on"
                    @blur="parseDate, validate()"
                  ></v-text-field>
                </ValidationProvider>
              </template>
              <v-date-picker
                v-model="deliveryDate"
                :min="new Date().toJSON()"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col :lg="4" :md="4" :cols="12">
            <ValidationProvider
              rules="required"
              v-slot="{ errors, valid }"
              name="Time Slot"
            >
              <v-select
                :readonly="$props.readonly"
                :loading="loading"
                label="Time Slot"
                prepend-inner-icon="mdi-clock"
                :append-icon="$props.readonly ? null : 'mdi-plus'"
                :items="time_slots"
                :error-messages="errors"
                :success="valid"
                item-text="label"
                v-model="deliveryTime"
                @input="setCost()"
                @click:append="$props.readonly ? null : addTimeSlotDialog()"
                @change="validate()"
              ></v-select>
            </ValidationProvider>
          </v-col>
          <v-col :md="2" :cols="4">
            <ValidationProvider
              rules="required|min_value:0"
              v-slot="{ errors, valid }"
              name="Fees"
            >
              <v-text-field
                :readonly="$props.readonly"
                type="number"
                label="Fees"
                prefix="$"
                :error-messages="errors"
                :success="valid"
                :min="0"
                v-model="deliveryFeesPrice"
                @input="validate()"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <v-row align="center" justify="space-between">
          <v-col :md="10" :cols="12">
            <ValidationProvider
              rules="required"
              v-slot="{ errors, valid }"
              name="Location"
            >
              <v-select
                :readonly="$props.readonly"
                :error-messages="errors"
                :success="valid"
                :loading="loading"
                label="Location"
                :items="store_pickups"
                item-text="name"
                return-object
                v-model="storePickup"
                prepend-inner-icon="mdi-store"
                @change="validate()"
              ></v-select>
            </ValidationProvider>
          </v-col>
        </v-row>
      </v-form>
    </ValidationObserver>
    <v-row>
      <orderNotes v-if="!hideNotes" />
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    hideNotes: Boolean,
    readonly: Boolean
  },

  mounted() {
    this.getStores();
    this.validate();

    if (this.order_delivery_store_pickup) {
      this.storePickup = this.order_delivery_store_pickup;
    }

    if (this.deliveryTime) {
      this.time_slots.push(this.deliveryTime);
    }

    EventBus.$on("delivery-timeslot", event => {
      console.log(event);
      if (event.payload) {
        this.time_slots.push(event.payload);
        this.deliveryTime = event.payload.label;
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("delivery-timeslot");
  },

  data() {
    return {
      loading: false,
      time_slots: [],
      datePicker: false,
      store_pickups: [],
      selected_store_pickup: null,
      delivery_amount: null,

      dialog: {
        show: false,
        width: 600,
        fullscreen: false,
        title: "",
        titleCloseBtn: false,
        icon: "",
        component: "",
        content: "",
        model: "",
        persistent: false
      }
    };
  },

  watch: {
    validateOption() {
      this.validate();
    }
  },

  computed: {
    ...mapState("cart", [
      "delivery",
      "delivery_fees_price",
      "order_delivery_store_pickup"
    ]),

    deliveryDate: {
      get() {
        return this.delivery.date;
      },
      set(value) {
        this.setDeliveryDate(value);
      }
    },
    storePickup: {
      get() {
        return this.selected_store_pickup;
      },
      set(value) {
        this.selected_store_pickup = value;
        this.setDeliveryStorePickupId(value.id);
      }
    },
    deliveryTime: {
      get() {
        return this.delivery.time;
      },
      set(value) {
        this.setDeliveryTime(value);
      }
    },
    deliveryFeesPrice: {
      get() {
        return this.delivery_amount;
      },
      set(value) {
        this.delivery_amount = value;
        this.setDeliveryFeesPrice({
          amount: Math.round(value * 10000) / 100
        });
      }
    },
    dateFormatted() {
      if (this.delivery.date) {
        return this.parseDate(this.delivery.date);
      } else {
        return null;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", [
      "setDeliveryFeesPrice",
      "setDeliveryStorePickupId",
      "setDeliveryDate",
      "setDeliveryTime",
      "setIsValid"
    ]),

    addTimeSlotDialog() {
      const payload = {
        show: true,
        title: "Add time slot",
        titleCloseBtn: true,
        icon: "mdi-clock",
        component: "timeSlotForm",
        persistent: true,
        eventChannel: "delivery-timeslot"
      };
      this.setDialog(payload);
    },
    validate() {
      this.$nextTick(() => {
        this.$refs.inStorePickupValidation.validate().then(result => {
          this.setIsValid(result);
        });
      });
    },
    parseDate(d) {
      if (d.length) {
        const [year, month, day] = d.split("-");
        return `${month}/${day}/${year}`;
      } else {
        return null;
      }
    },
    getStores() {
      const payload = { method: "get", url: "store-pickups" };
      this.request(payload).then(response => {
        this.store_pickups = response.data;
      });
    },
    setCost(item) {
      if (_.has(item, "cost")) {
        this.deliveryFeesPrice = item.cost;
      } else {
        this.deliveryFeesPrice = 0;
      }
    }
  }
};
</script>
