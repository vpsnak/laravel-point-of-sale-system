<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider
          rules="required|max:191"
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
          rules="required"
          v-slot="{ errors, valid }"
          name="Stores"
        >
          <v-select
            :readonly="$props.readonly"
            v-model="formFields.store_id"
            label="Stores"
            :items="stores"
            item-text="name"
            item-value="id"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-select>
        </ValidationProvider>
        <v-checkbox v-model="formFields.active" label="Active"></v-checkbox>
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
            <v-btn v-if="!model" @click="clear" color="orange">clear</v-btn>
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
        store_id: null,
        active: true
      },
      stores: []
    };
  },
  mounted() {
    this.getAllStores();
    this.defaultValues = { ...this.formFields };
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
    }
  },
  methods: {
    ...mapActions({
      getAll: "getAll",
      create: "create"
    }),
    submit() {
      this.loading = true;
      let payload = {
        model: "cash-registers",
        data: { ...this.formFields }
      };
      this.create(payload)
        .then(() => {
          this.$emit("submit", {
            action: "paginate",
            notification: {
              msg: "Cash Register added successfully",
              type: "success"
            }
          });
        })
        .finally(() => {
          this.clear();
          this.loading = false;
        });
    },
    clear() {
      this.formFields = { ...this.defaultValues };
    },
    getAllStores() {
      this.loading = true;
      this.getAll({
        model: "stores"
      })
        .then(response => {
          this.stores = response;
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
