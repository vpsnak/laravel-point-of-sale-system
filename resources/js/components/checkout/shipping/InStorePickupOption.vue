<template>
  <div>
    <v-container>
      <ValidationObserver ref="inStorePickupValidation">
        <v-form>
          <v-row align="center" justify="center">
            <v-col :cols="4" :lg="2">
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
                      v-model="dateFormatted"
                      label="Date"
                      prepend-icon="event"
                      :error-messages="errors"
                      :success="valid"
                      v-on="on"
                      @blur="parseDate, validate()"
                      readonly
                    ></v-text-field>
                  </ValidationProvider>
                </template>
                <v-date-picker
                  v-model="deliveryDate"
                  :min="new Date().toJSON()"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col :cols="4" :lg="3">
              <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Time Slot"
              >
                <v-select
                  :loading="loading"
                  label="Time Slot"
                  prepend-icon="mdi-clock"
                  append-icon="mdi-plus"
                  :items="time_slots"
                  :error-messages="errors"
                  :success="valid"
                  item-text="label"
                  v-model="timeSlot"
                  @input="setCost()"
                  @click:append="addTimeSlotDialog()"
                  @change="validate()"
                ></v-select>
              </ValidationProvider>
            </v-col>
            <v-col :cols="4" :lg="1">
              <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Fees"
              >
                <v-text-field
                  type="number"
                  label="Fees"
                  prefix="$"
                  :error-messages="errors"
                  :success="valid"
                  :min="0"
                  v-model="shippingCost"
                  @input="validate()"
                ></v-text-field>
              </ValidationProvider>
            </v-col>
          </v-row>
          <v-row align="center" justify="center">
            <v-col :cols="6">
              <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Location"
              >
                <v-select
                  :error-messages="errors"
                  :success="valid"
                  :loading="loading"
                  label="Location"
                  :items="store_pickups"
                  item-text="name"
                  return-object
                  v-model="storePickup"
                  prepend-icon="mdi-store"
                  @change="validate()"
                ></v-select>
              </ValidationProvider>
            </v-col>
          </v-row>
        </v-form>
      </ValidationObserver>
    </v-container>
    <orderNotes />
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
  mounted() {
    this.getStores();
    this.validate();

    EventBus.$on("shipping-timeslot", event => {
      if (event.payload) {
        this.time_slots.push(event.payload);
        this.timeSlot = event.payload.label;
      }
    });
  },

  beforeDestroy() {
    EventBus.$off();
  },

  data() {
    return {
      loading: false,
      time_slots: [],
      datePicker: false,
      store_pickups: [],
      selected_store_pickup: null,

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
    ...mapState("cart", ["delivery", "shipping_cost"]),

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
    timeSlot: {
      get() {
        return this.delivery.time;
      },
      set(value) {
        this.setDeliveryTime(value);
      }
    },
    shippingCost: {
      get() {
        return this.shipping_cost;
      },
      set(value) {
        this.setShippingCost(value);
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
    ...mapActions(["postRequest", "getAll"]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", [
      "setShippingCost",
      "setDeliveryStorePickupId",
      "setDeliveryDate",
      "setDeliveryTime",
      "setIsValid"
    ]),

    addTimeSlotDialog() {
      this.setDialog({
        show: true,
        title: "Add time slot",
        titleCloseBtn: true,
        icon: "mdi-clock",
        component: "timeSlotForm",
        persistent: true,
        eventChannel: "shipping-timeslot"
      });
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
        let [year, month, day] = d.split("-");
        return `${month}/${day}/${year}`;
      } else {
        return null;
      }
    },
    getStores() {
      this.getAll({ model: "store-pickups" }).then(response => {
        this.store_pickups = response;
      });
    },
    setCost(item) {
      if (_.has(item, "cost")) {
        this.shippingCost = item.cost;
      }
    }
  }
};
</script>
