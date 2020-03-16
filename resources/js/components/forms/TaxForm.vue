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
            label="Name"
            v-model="formFields.name"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|min:0|max_value:100"
          v-slot="{ errors, valid }"
          name="Percentage"
        >
          <v-text-field
            :readonly="$props.readonly"
            label="Percentage"
            v-model="formFields.percentage"
            :min="0"
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
              color="primary"
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
        name: "",
        percentage: ""
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
          url: "taxes/update",
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
          url: "taxes/create",
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
