<template>
  <div>
    <v-container fluid>
      <ValidationObserver ref="deliveryValidation">
        <v-form>
          <v-row align="center" justify="center">
            <v-col :cols="12" :lg="7">
              <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Billing address"
              >
                <v-select
                  @change="validate"
                  :items="addresses"
                  prepend-icon="mdi-receipt"
                  label="Billing address"
                  v-model="billingAddressId"
                  :item-text="getAddressText"
                  item-value="id"
                  :error-messages="errors"
                  :success="valid"
                ></v-select>
              </ValidationProvider>
            </v-col>

            <div>
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn
                    :disabled="!billingAddressId"
                    icon
                    v-on="on"
                    @click="addressDialog('billing', billingAddressId)"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                </template>
                <span>Edit selected address</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on" @click="addressDialog('billing', null)">
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </template>
                <span>New billing address</span>
              </v-tooltip>
            </div>

            <v-col :cols="10" :lg="7">
              <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Delivery address"
              >
                <v-select
                  @input="getTimeSlots"
                  @change="validate"
                  :items="addresses"
                  prepend-icon="mdi-map-marker"
                  label="Delivery address"
                  v-model="deliveryAddressId"
                  :item-text="getAddressText"
                  item-value="id"
                  :error-messages="errors"
                  :success="valid"
                ></v-select>
              </ValidationProvider>
            </v-col>

            <div>
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn
                    :disabled="!deliveryAddressId"
                    icon
                    v-on="on"
                    @click="addressDialog('delivery', deliveryAddressId)"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                </template>
                <span>Edit selected address</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn
                    icon
                    v-on="on"
                    @click="addressDialog('delivery', null)"
                  >
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </template>
                <span>New delivery address</span>
              </v-tooltip>
            </div>

            <v-col :cols="12" :lg="6">
              <addressDeliveryForm
                v-if="selected_delivery_address"
                :model="selected_delivery_address"
                readonly
              ></addressDeliveryForm>
            </v-col>
          </v-row>

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
                      prepend-inner-icon="event"
                      :error-messages="errors"
                      :success="valid"
                      v-on="on"
                      @input="getTimeSlots"
                      @blur="parseDate, validate"
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
            <v-col :cols="4" :lg="3">
              <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Time Slot"
              >
                <v-select
                  :loading="loading"
                  label="Time Slot"
                  prepend-inner-icon="mdi-clock"
                  append-icon="mdi-plus"
                  :items="timeSlots"
                  :error-messages="errors"
                  :success="valid"
                  item-text="label"
                  v-model="deliveryTime"
                  @input="setCost"
                  @click:append="addTimeSlotDialog()"
                  @change="validate"
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
                  :error="errors.length ? true : false"
                  :success="valid"
                  :min="0"
                  v-model="shippingCost"
                  @input="validate"
                ></v-text-field>
              </ValidationProvider>
            </v-col>
            <v-col :cols="6" :lg="2">
              <v-select
                :loading="loading"
                label="Occasion"
                :items="occasions"
                item-text="label"
                item-value="id"
                v-model="deliveryOccasion"
                prepend-inner-icon="mdi-star-face"
              ></v-select>
            </v-col>
          </v-row>
        </v-form>
      </ValidationObserver>
    </v-container>
    <orderNotes />
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
  mounted() {
    this.validate();

    EventBus.$on("shipping-timeslot", event => {
      if (event.payload) {
        this.timeSlots.push(event.payload);
        this.setDeliveryTime(event.payload.label);
      }
    });

    EventBus.$on("billing-address", event => {
      if (event.payload) {
        this.addresses.push(event.payload);
        this.billingAddressId = event.payload;
      }
    });

    EventBus.$on("delivery-address", event => {
      if (event.payload) {
        this.addresses.push(event.payload);
        this.deliveryAddressId = event.payload;
      }
    });
  },

  beforeDestroy() {
    EventBus.$off();
  },

  data() {
    return {
      selected_delivery_address: null,
      selected_billing_address: null,
      loading: false,
      time_slots: [],
      datePicker: false
    };
  },
  watch: {
    validateOption() {
      this.validate();
    }
  },

  computed: {
    ...mapState("cart", [
      "customer",
      "occasions",
      "delivery",
      "billing_address_id",
      "shipping_cost"
    ]),

    deliveryTime: {
      get() {
        return this.delivery.time;
      },
      set(value) {
        this.setDeliveryTime(value);
      }
    },

    deliveryOccasion: {
      get() {
        return this.delivery.occasion;
      },
      set(value) {
        this.setDeliveryOccasion(value);
      }
    },
    timeSlots: {
      get() {
        return this.time_slots;
      },
      set(value) {
        this.time_slots = value;
      }
    },
    dateFormatted() {
      if (this.delivery.date) {
        return this.parseDate(this.delivery.date);
      } else {
        return null;
      }
    },
    billingAddressId: {
      get() {
        return this.billing_address_id;
      },
      set(value) {
        this.selected_billing_address = this.getAddressById(value);
        this.setBillingAddressId(value);
      }
    },
    deliveryAddressId: {
      get() {
        return this.delivery.address_id;
      },
      set(value) {
        if (value) {
          this.selected_delivery_address = this.getAddressById(value);
          this.setDeliveryAddressId(value);
        }
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
    deliveryDate: {
      get() {
        return this.delivery.date;
      },
      set(value) {
        this.setDeliveryDate(value);
      }
    },
    addresses: {
      get() {
        if (this.customer && !this.billingAddressId) {
          const billing_address = _.filter(this.customer.addresses, [
            "is_default_billing",
            true
          ]);

          this.billingAddressId = billing_address.length
            ? billing_address[0].id
            : null;
        }
        if (this.customer && !this.deliveryAddressId) {
          const shipping_address = _.filter(this.customer.addresses, [
            "is_default_shipping",
            true
          ]);

          this.deliveryAddressId = shipping_address.length
            ? shipping_address[0].id
            : null;
        }

        return this.customer.addresses;
      },
      set(value) {
        this.setCustomerAddress(value);
      }
    }
  },

  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("cart", [
      "setDeliveryAddressId",
      "setDeliveryDate",
      "setShippingCost",
      "setDeliveryTime",
      "setDeliveryOccasion",
      "setBillingAddressId",
      "setCustomerAddress",
      "setIsValid"
    ]),
    ...mapActions(["postRequest"]),

    getAddressById(id) {
      return _.find(this.customer.addresses, { id: id });
    },

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
        this.$refs.deliveryValidation.validate().then(result => {
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
    addressDialog(action, address_id) {
      let icon;
      let title;
      let data;
      let eventChannel;

      if (action === "delivery") {
        icon = "mdi-map-marker";
        title = address_id ? "Edit delivery address" : "New delivery address";
        data = this.selected_delivery_address;
        eventChannel = "delivery-address";
      } else {
        icon = "mdi-receipt";
        title = address_id ? "Edit billing address" : "New billing address";
        data = this.selected_billing_address;
        eventChannel = "billing-address";
      }

      this.setDialog({
        show: true,
        width: 600,
        fullscreen: false,
        title: title,
        titleCloseBtn: true,
        icon: icon,
        component: "addressDeliveryForm",
        content: "",
        model: data,
        persistent: true,
        eventChannel
      });
    },
    setCost(item) {
      if (_.has(item, "cost")) {
        this.shippingCost = item.cost;
      } else {
        this.shippingCost = null;
      }
    },
    getTimeSlots() {
      if (this.delivery.address_id && this.delivery.date) {
        this.loading = true;
        let payload = {
          url: "shipping/timeslot",
          data: {
            postcode: this.selected_delivery_address.postcode,
            date: this.delivery.date
          }
        };
        this.postRequest(payload)
          .then(response => {
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
        item.address_region.default_name +
        ", " +
        item.postcode +
        ", " +
        item.address_country
      );
    }
  }
};
</script>
