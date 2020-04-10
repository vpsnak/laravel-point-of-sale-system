<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
      <v-row>
        <v-col :cols="6">
          <ValidationProvider
            rules="required|max:100"
            v-slot="{ errors }"
            name="Name"
          >
            <v-text-field
              v-model="formFields.name"
              label="Name"
              :readonly="$props.readonly"
              :disabled="loading"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="6">
          <ValidationProvider
            rules="required|min:0|max_value:50"
            v-slot="{ errors }"
            name="Percentage"
          >
            <v-text-field
              v-model="formFields.percentage"
              :readonly="$props.readonly"
              label="Percentage"
              prefix="%"
              :min="0"
              :disabled="loading"
              :error-messages="errors"
              style="width: 100px;"
            />
          </ValidationProvider>
        </v-col>
      </v-row>
    </v-container>
    <v-container v-if="!$props.readonly">
      <v-row justify="center">
        <v-btn
          class="mr-4"
          type="submit"
          :loading="loading"
          :disabled="invalid || loading"
          color="primary"
          >submit
        </v-btn>
      </v-row>
    </v-container>
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
      formFields: {
        name: "",
        percentage: ""
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

  beforeDestroy() {
    this.$off("submit");
  },

  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;
      const payload = {
        method: this.formFields.id ? "patch" : "post",
        url: this.formFields.id ? "taxes/update" : "taxes/create",
        data: this.formFields
      };

      this.request(payload)
        .then(() => {
          this.$emit("submit", {
            action: "paginate"
          });
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
