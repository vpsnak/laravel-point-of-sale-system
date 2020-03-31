<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider
          rules="required|max:100"
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
        <ValidationProvider
          rules="required|max:100"
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
        <ValidationProvider
          rules="required|between:0.01,1000"
          v-slot="{ errors }"
          name="Price"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="price"
            type="number"
            label="Price"
            :disabled="loading"
            :error-messages="errors"
          />
        </ValidationProvider>
        <v-row justify="space-around">
          <v-switch
            v-if="$props.model ? true : false"
            v-model="formFields.bulk_action"
            label="Bulk Action"
            :readonly="$props.readonly"
          />

          <v-switch
            :readonly="$props.readonly"
            v-model="formFields.enabled"
            label="Enabled"
            :disabled="loading"
          />
        </v-row>
        <ValidationProvider
          rules="required_if:bulk_action|min_value:1|numeric"
          v-slot="{ errors }"
          name="Qty"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-if="formFields.bulk_action"
            type="number"
            v-model="formFields.qty"
            label="Qty"
            :disabled="loading"
            :error-messages="errors"
            :min="1"
          />
        </ValidationProvider>
      </v-container>
      <v-container>
        <v-row v-if="!$props.readonly">
          <v-col cols="12" align="center" justify="center">
            <v-btn
              class="mr-4"
              type="submit"
              :loading="loading"
              :disabled="invalid || loading"
              color="primary"
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
      price_amount: null,
      loading: false,
      formFields: {
        name: null,
        code: null,
        enabled: false,
        enabled_at: null,
        price: {
          amount: null,
          currency: null
        },
        bulk_action: false,
        qty: 1
      }
    };
  },

  mounted() {
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };

      this.formFields.enabled = this.formFields.enabled_at ? true : false;

      this.price_amount = this.parsePrice(this.formFields.price).toFormat(
        "0.00"
      );
    }
  },

  computed: {
    price: {
      get() {
        return this.price_amount;
      },
      set(value) {
        this.price_amount = value;
        this.formFields.price = this.parsePrice(
          Math.round(value * 10000) / 100
        ).toJSON();
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;

      if (this.$props.model) {
        this.request({
          method: "patch",
          url: "giftcards/update",
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
          url: "giftcards/create",
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
