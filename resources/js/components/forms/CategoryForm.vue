<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
      <v-col :cols="6">
        <ValidationProvider
          rules="required|max:100"
          v-slot="{ errors }"
          name="Name"
        >
          <v-text-field
            :readonly="$props.readonly"
            :error-messages="errors"
            v-model="formFields.name"
            label="Name"
            required
          />
        </ValidationProvider>
      </v-col>
      <v-col :cols="6">
        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.is_enabled"
          label="Enabled"
        />
      </v-col>
    </v-container>
    <v-container>
      <v-row v-if="!$props.readonly" justify="center">
        <v-btn
          type="submit"
          @click.prevent="submit"
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
        is_enabled: true
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
        url: this.formFields.id ? "categories/update" : "categories/create",
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
