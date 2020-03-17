<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <v-row>
          <v-col :cols="6">
            <ValidationProvider
              rules="required|max:255"
              v-slot="{ errors }"
              name="Name"
            >
              <v-text-field
                :readonly="$props.readonly"
                v-model="formFields.name"
                label="Name"
                :disabled="loading"
                :error-messages="errors"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col :cols="6">
            <ValidationProvider
              rules="required|max:255"
              v-slot="{ errors }"
              name="Code"
            >
              <v-text-field
                :readonly="$props.readonly"
                v-model="formFields.code"
                label="Code"
                :disabled="loading"
                :error-messages="errors"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <v-row>
          <v-col :cols="6">
            <ValidationProvider
              rules="required"
              v-slot="{ errors }"
              name="Discount type"
            >
              <v-select
                :readonly="$props.readonly"
                v-model="formFields.discount.type"
                label="Discount type"
                :items="discount_types"
                :disabled="loading"
                :error-messages="errors"
              ></v-select>
            </ValidationProvider>
          </v-col>
          <v-col :cols="6">
            <ValidationProvider
              :rules="discountAmountRules"
              v-slot="{ errors }"
              name="Discount amount"
            >
              <v-text-field
                :readonly="$props.readonly"
                v-model="discountAmountPrice"
                type="number"
                label="Discount amount"
                :disabled="loading || !formFields.discount.type"
                :error-messages="errors"
                :max="discountAmountRules"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
        <v-row>
          <v-col :cols="4">
            <v-menu
              :readonly="$props.readonly"
              v-model="fromDatePicker"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
              <template v-slot:activator="{ on }">
                <ValidationProvider
                  rules="required"
                  v-slot="{ errors }"
                  name="From"
                >
                  <v-text-field
                    v-model="fromDate"
                    label="From:"
                    prepend-inner-icon="event"
                    :error-messages="errors"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </ValidationProvider>
              </template>
              <v-date-picker
                :readonly="$props.readonly"
                v-model="formFields.from"
                @input="fromDatePicker = false"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col :cols="4">
            <v-menu
              :readonly="$props.readonly"
              v-model="toDatePicker"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
              <template v-slot:activator="{ on }">
                <ValidationProvider
                  rules="required"
                  v-slot="{ errors }"
                  name="To"
                >
                  <v-text-field
                    v-model="toDate"
                    label="To:"
                    prepend-inner-icon="event"
                    readonly
                    v-on="on"
                    :error-messages="errors"
                  ></v-text-field>
                </ValidationProvider>
              </template>
              <v-date-picker
                :readonly="$props.readonly"
                v-model="formFields.to"
                @input="toDatePicker = false"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col :cols="4">
            <ValidationProvider
              rules="required|numeric|max:11"
              v-slot="{ errors, valid }"
              name="No. of uses"
            >
              <v-text-field
                :readonly="$props.readonly"
                v-model="formFields.uses"
                type="number"
                label="No. of uses"
                prepend-inner-icon="mdi-pound"
                :disabled="loading"
                :error-messages="errors"
                :success="valid"
              ></v-text-field>
            </ValidationProvider>
          </v-col>
        </v-row>
      </v-container>
      <v-container>
        <v-row v-if="!$props.readonly">
          <v-col :cols="12" align="center" justify="center">
            <v-btn
              color="primary"
              class="mr-4"
              type="submit"
              :loading="loading"
              :disabled="invalid || loading"
              >submit
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>
<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Object,
    readonly: Boolean
  },

  data() {
    return {
      discount_amount: null,
      fromDatePicker: false,
      toDatePicker: false,
      loading: false,
      discount_types: [
        {
          text: "Flat",
          value: "flat"
        },
        {
          text: "Percentage",
          value: "percentage"
        }
      ],
      formFields: {
        name: null,
        code: null,
        uses: null,
        discount: {
          type: null,
          amount: null
        },
        from: null,
        to: null
      }
    };
  },

  mounted() {
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
    }
  },

  computed: {
    discountAmountPrice: {
      get() {
        return this.discount_amount;
      },
      set(value) {
        this.discount_amount = value;
        if (this.formFields.discount.type === "flat") {
          this.formFields.discount.amount = Math.round(value * 10000) / 100;
        } else {
          this.formFields.discount.amount = value;
        }
      }
    },
    discountAmountRules() {
      switch (this.formFields.discount.type) {
        case "flat":
          return "required|between:0.01,100";
        case "percentage":
          return "required|between:1,99";
      }

      return null;
    },
    fromDate() {
      if (this.formFields.from) {
        return this.parseDate(this.formFields.from);
      } else {
        return null;
      }
    },
    toDate() {
      if (this.formFields.to) {
        return this.parseDate(this.formFields.to);
      } else {
        return null;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    parseDate(d) {
      if (d.length) {
        let [year, month, day] = d.split("-");
        return `${month}/${day}/${year}`;
      } else {
        return null;
      }
    },
    submit() {
      this.loading = true;

      if (this.$props.model) {
        this.request({
          method: "patch",
          url: "coupons/update",
          data: { ...this.formFields }
        })
          .then(() => {
            this.$emit("submit", {
              action: "paginate"
            });
          })
          .finally(() => {
            this.loading = false;
          });
      } else {
        this.request({
          method: "post",
          url: "coupons/create",
          data: { ...this.formFields }
        })
          .then(() => {
            this.$emit("submit", {
              action: "paginate"
            });
          })
          .finally(() => {
            this.loading = false;
          });
      }
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
