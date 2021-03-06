<template>
  <v-container>
    <ValidationObserver ref="inStorePickupValidation">
      <v-row align="center" justify="space-between">
        <v-col :cols="$props.readonly ? 12 : 10">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Location"
          >
            <v-select
              :readonly="$props.readonly"
              :error-messages="errors"
              :loading="loading"
              label="Location"
              :items="store_pickups"
              item-text="name"
              return-object
              v-model="storePickup"
              prepend-inner-icon="mdi-storefront"
              @change="validate()"
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="1" v-if="!$props.readonly">
          <v-btn
            icon
            @click="storePickupDialog(selected_store_pickup)"
            :disabled="!selected_store_pickup"
          >
            <v-icon v-text="'mdi-pencil'" />
          </v-btn>
        </v-col>

        <v-col :cols="1" v-if="!$props.readonly">
          <v-btn icon @click="storePickupDialog()">
            <v-icon v-text="'mdi-plus'" />
          </v-btn>
        </v-col>
      </v-row>
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
                v-slot="{ errors }"
                name="Date"
              >
                <v-text-field
                  readonly
                  v-model="dateFormatted"
                  label="Date"
                  prepend-inner-icon="event"
                  :error-messages="errors"
                  v-on="$props.readonly ? null : on"
                  @blur="parseDate, validate()"
                />
              </ValidationProvider>
            </template>
            <v-date-picker v-model="deliveryDate" :min="new Date().toJSON()" />
          </v-menu>
        </v-col>
        <v-col :lg="4" :md="4" :cols="12">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
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
            v-slot="{ errors }"
            name="Fees"
          >
            <v-text-field
              :readonly="$props.readonly"
              type="number"
              label="Fees"
              prefix="$"
              :error-messages="errors"
              :min="0"
              v-model="deliveryFeesPrice"
              @input="validate()"
            ></v-text-field>
          </ValidationProvider>
        </v-col>
      </v-row>
      <v-row v-if="!hideNotes">
        <orderNotes />
      </v-row>
    </ValidationObserver>
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
    this.initEvents();

    this.delivery_amount = this.parsePrice(this.delivery_fees_price).toFormat(
      "0.00"
    );

    if (this.order_delivery_store_pickup) {
      this.storePickup = this.order_delivery_store_pickup;
    }

    if (this.deliveryTime) {
      this.time_slots.push(this.deliveryTime);
    }
  },

  beforeDestroy() {
    EventBus.$off("delivery-timeslot");
    EventBus.$off("new-instore-pickup");
    EventBus.$off("edit-instore-pickup");
  },

  data() {
    return {
      loading: false,
      time_slots: [],
      datePicker: false,
      store_pickups: [],
      selected_store_pickup: null,
      delivery_amount: null
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

    initEvents() {
      EventBus.$on("delivery-timeslot", event => {
        if (event.payload) {
          this.time_slots.push(event.payload);
          this.deliveryTime = event.payload.label;
        }
      });

      EventBus.$on("new-instore-pickup", event => {
        console.log(event);
        if (event.payload) {
          this.store_pickups.push(event.payload);
          this.storePickup = event.payload;
        }
      });

      EventBus.$on("edit-instore-pickup", event => {
        if (event.payload) {
          const index = _.findIndex(this.store_pickups, {
            id: event.payload.id
          });
          this.store_pickups[index] = event.payload;
          this.storePickup = event.payload;
        }
      });
    },
    storePickupDialog(model) {
      const payload = {
        show: true,
        title: model ? `Edit pickup store ${model.name}` : "Add pickup store",
        titleCloseBtn: true,
        icon: "mdi-storefront",
        component: "storePickupForm",
        component_props: { model },
        persistent: true,
        eventChannel: model ? "edit-instore-pickup" : "new-instore-pickup"
      };
      this.setDialog(payload);
    },
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
