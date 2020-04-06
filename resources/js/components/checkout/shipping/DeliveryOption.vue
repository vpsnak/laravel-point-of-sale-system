<template>
  <v-container fluid>
    <ValidationObserver ref="deliveryValidation" tag="v-form">
      <v-row align="center" justify="center">
        <v-col :cols="$props.readonly ? 12 : 10">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Billing address"
          >
            <v-select
              :readonly="$props.readonly"
              @change="validate()"
              :items="addresses"
              prepend-icon="mdi-receipt"
              label="Billing address"
              v-model="billingAddress"
              :item-text="getAddressText"
              return-object
              :error-messages="errors"
            ></v-select>
          </ValidationProvider>
        </v-col>

        <v-col :cols="2" v-if="!$props.readonly">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn
                :disabled="!billingAddress"
                icon
                v-on="on"
                @click="addressDialog('billing', true)"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
            </template>
            <span>Edit selected address</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn icon v-on="on" @click="addressDialog('billing', false)">
                <v-icon>mdi-plus</v-icon>
              </v-btn>
            </template>
            <span>New billing address</span>
          </v-tooltip>
        </v-col>

        <v-col :cols="$props.readonly ? 12 : 10">
          <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="Delivery address"
          >
            <v-select
              :readonly="$props.readonly"
              @input="getTimeSlots"
              @change="validate()"
              :items="addresses"
              prepend-icon="mdi-map-marker"
              label="Delivery address"
              v-model="deliveryAddress"
              :item-text="getAddressText"
              return-object
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>

        <v-col :cols="2" v-if="!$props.readonly">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn
                :disabled="!deliveryAddress"
                icon
                v-on="on"
                @click="addressDialog('delivery', true)"
              >
                <v-icon v-text="'mdi-pencil'" />
              </v-btn>
            </template>
            <span v-text="'Edit selected address'" />
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn icon v-on="on" @click="addressDialog('delivery', false)">
                <v-icon v-text="'mdi-plus'" />
              </v-btn>
            </template>
            <span v-text="'New delivery address'" />
          </v-tooltip>
        </v-col>

        <v-col :cols="12">
          <addressForm
            v-if="deliveryAddress"
            :key="deliveryAddress.id"
            :model="deliveryAddress"
            readonly
          />
        </v-col>
      </v-row>

      <v-row align="center" justify="center">
        <v-col :cols="3">
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
                  v-model="dateFormatted"
                  label="Date"
                  prepend-inner-icon="event"
                  :error-messages="errors"
                  v-on="on"
                  @input="getTimeSlots"
                  @blur="parseDate, validate()"
                  readonly
                ></v-text-field>
              </ValidationProvider>
            </template>
            <v-date-picker
              v-model="deliveryDate"
              @input="getTimeSlots"
              :min="new Date().toJSON()"
            ></v-date-picker>
          </v-menu>
        </v-col>
        <v-col :cols="3">
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
              :items="timeSlots"
              :error-messages="errors"
              item-text="label"
              v-model="deliveryTime"
              @input="setCost"
              @click:append="$props.readonly ? null : addTimeSlotDialog()"
              @change="validate()"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="2">
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
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="4">
          <v-select
            :readonly="$props.readonly"
            label="Occasion"
            :items="occasions"
            item-text="label"
            item-value="id"
            v-model="deliveryOccasion"
            prepend-inner-icon="mdi-star-face"
          />
        </v-col>
      </v-row>
    </ValidationObserver>
    <v-row>
      <orderNotes v-if="!hideNotes" />
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../../plugins/eventBus";

export default {
  props: {
    hideNotes: Boolean,
    readonly: Boolean,
  },

  mounted() {
    this.validate();

    this.initEventBus();

    if (this.order_billing_address) {
      this.billingAddress = this.order_billing_address;
    }
    if (this.order_delivery_address) {
      this.deliveryAddress = this.order_delivery_address;
    }

    if (this.deliveryTime) {
      this.time_slots.push(this.deliveryTime);
    }
  },

  beforeDestroy() {
    EventBus.$off("shipping-timeslot");
    EventBus.$off("billing-address-new");
    EventBus.$off("billing-address-edit");
    EventBus.$off("delivery-address-new");
    EventBus.$off("delivery-address-edit");
  },

  data() {
    return {
      delivery_amount: null,
      delivery_address: null,
      billing_address: null,
      loading: false,
      time_slots: [],
      datePicker: false,
    };
  },
  watch: {
    validateOption() {
      this.validate();
    },
  },

  computed: {
    ...mapState("cart", [
      "order_billing_address",
      "order_delivery_address",
      "customer",
      "occasions",
      "delivery",
      "billing_address_id",
      "shipping_cost",
    ]),

    deliveryFeesPrice: {
      get() {
        return this.delivery_amount;
      },
      set(value) {
        this.delivery_amount = value;
        this.setDeliveryFeesPrice({
          amount: Math.round(value * 10000) / 100,
        });
      },
    },
    deliveryTime: {
      get() {
        return this.delivery.time;
      },
      set(value) {
        this.setDeliveryTime(value);
      },
    },
    deliveryOccasion: {
      get() {
        return this.delivery.occasion;
      },
      set(value) {
        this.setDeliveryOccasion(value);
      },
    },
    timeSlots: {
      get() {
        return this.time_slots;
      },
      set(value) {
        this.time_slots = value;
      },
    },
    dateFormatted() {
      if (this.delivery.date) {
        return this.parseDate(this.delivery.date);
      } else {
        return null;
      }
    },
    billingAddress: {
      get() {
        return this.billing_address;
      },
      set(value) {
        this.billing_address = value;
        if (_.has(value, "id")) {
          this.setBillingAddressId(value.id);
        }
      },
    },
    deliveryAddress: {
      get() {
        return this.delivery_address;
      },
      set(value) {
        this.delivery_address = value;
        if (_.has(value, "id")) {
          this.setDeliveryAddressId(value.id);
        }
      },
    },
    deliveryDate: {
      get() {
        return this.delivery.date;
      },
      set(value) {
        this.setDeliveryDate(value);
      },
    },
    addresses: {
      get() {
        if (this.customer && !this.billingAddress) {
          const billing_address = _.filter(this.customer.addresses, [
            "is_default_billing",
            true,
          ]);

          this.billingAddress = billing_address.length
            ? billing_address[0]
            : null;
        }
        if (this.customer && !this.deliveryAddress) {
          const delivery_address = _.filter(this.customer.addresses, [
            "is_default_shipping",
            true,
          ]);

          this.deliveryAddress = delivery_address.length
            ? delivery_address[0]
            : null;
        }

        return this.customer.addresses;
      },
      set(value) {
        this.setCustomerAddress(value);
      },
    },
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", [
      "setDeliveryAddressId",
      "setDeliveryDate",
      "setDeliveryFeesPrice",
      "setDeliveryTime",
      "setDeliveryOccasion",
      "setBillingAddressId",
      "setCustomerAddress",
      "setIsValid",
    ]),
    ...mapActions("requests", ["request"]),

    getAddressById(id) {
      return _.find(this.customer.addresses, { id: id });
    },

    initEventBus() {
      EventBus.$on("shipping-timeslot", (event) => {
        if (event.payload) {
          this.timeSlots.push(event.payload);
          this.setDeliveryTime(event.payload.label);
        }
      });

      EventBus.$on("billing-address-new", (event) => {
        if (event.payload) {
          this.addresses.push(event.payload);
          this.billingAddress = event.payload;
        }
      });

      EventBus.$on("billing-address-edit", (event) => {
        if (event.payload) {
          const index = _.findIndex(this.addresses, {
            id: event.payload.id,
          });
          this.addresses[index] = event.payload;
          this.billingAddress = event.payload;
        }
      });

      EventBus.$on("delivery-address-new", (event) => {
        if (event.payload) {
          this.addresses.push(event.payload);
          this.deliveryAddress = event.payload;
        }
      });

      EventBus.$on("delivery-address-edit", (event) => {
        if (event.payload) {
          const index = _.findIndex(this.addresses, {
            id: event.payload.id,
          });
          this.addresses[index] = event.payload;
          this.deliveryAddress = event.payload;
        }
      });
    },

    addTimeSlotDialog() {
      const payload = {
        show: true,
        title: "Add time slot",
        titleCloseBtn: true,
        icon: "mdi-clock",
        component: "timeSlotForm",
        persistent: true,
        eventChannel: "shipping-timeslot",
      };
      this.setDialog(payload);
    },
    validate() {
      this.$nextTick(() => {
        this.$refs.deliveryValidation.validate().then((result) => {
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
    addressDialog(action, edit) {
      let icon;
      let title;
      let data;
      let eventChannel;

      if (action === "delivery") {
        icon = "mdi-map-marker";
        title = edit ? "Edit delivery address" : "New delivery address";
        data = edit ? this.deliveryAddress : null;
        eventChannel = edit ? "delivery-address-edit" : "delivery-address-new";
      } else {
        icon = "mdi-receipt";
        title = edit ? "Edit billing address" : "New billing address";
        data = edit ? this.billingAddress : null;
        eventChannel = edit ? "billing-address-edit" : "billing-address-new";
      }

      this.setDialog({
        show: true,
        width: 750,
        fullscreen: false,
        title: title,
        titleCloseBtn: true,
        icon: icon,
        component: "addressForm",
        content: "",
        model: data,
        persistent: true,
        eventChannel,
      });
    },
    setCost(item) {
      if (_.has(item, "cost")) {
        this.shippingCost = item.cost;
      } else {
        this.shippingCost = 0;
      }
    },
    getTimeSlots() {
      if (this.delivery.address_id && this.delivery.date) {
        this.loading = true;
        let payload = {
          method: "post",
          url: "shipping/timeslot",
          data: {
            postcode: this.delivery_address.postcode,
            date: this.delivery.date,
          },
        };
        this.request(payload)
          .then((response) => {
            this.timeSlots = response;
          })
          .finally(() => {
            this.loading = false;
          });
      }
    },
    getAddressText(item) {
      return (
        item.first_name +
        " " +
        item.last_name +
        " " +
        item.street +
        " " +
        item.city +
        ", " +
        item.region.name +
        ", " +
        item.postcode +
        ", " +
        item.region.country.iso3_code
      );
    },
  },
};
</script>
