<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider
          rules="required|max:255"
          v-slot="{ errors, valid }"
          name="Name"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.name"
            label="Name"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|max:255|numeric"
          v-slot="{ errors, valid }"
          name="Code"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.code"
            label="Code"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|between:0.01,1000"
          v-slot="{ errors, valid }"
          name="Price amount"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.price.amount"
            type="number"
            label="Price amount"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <v-row justify="space-around">
          <ValidationProvider vid="bulk_action">
            <v-switch
              :disabled="$props.model"
              :readonly="$props.readonly"
              v-model="formFields.bulk_action"
              label="Bulk Action"
            ></v-switch>
          </ValidationProvider>
          <v-switch
            :readonly="$props.readonly"
            v-model="formFields.enabled"
            label="Enabled"
            :disabled="loading"
          ></v-switch>
        </v-row>
        <ValidationProvider
          rules="required_if:bulk_action|max:5|min_value:1|numeric"
          v-slot="{ errors, valid }"
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
            :success="valid"
          ></v-text-field>
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
              color="secondary"
              >submit</v-btn
            >
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
      loading: false,
      defaultValues: {},
      formFields: {
        name: null,
        code: null,
        enabled: false,
        price: {
          type: null,
          amount: null
        },
        bulk_action: false,
        qty: 0
      }
    };
  },
  mounted() {
    this.defaultValues = { ...this.formFields };
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
    }
  },
  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;

      if (this.$props.model) {
        this.request({
          method: "patch",
          url: "gift-cards/update",
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
          url: "gift-cards/create",
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
