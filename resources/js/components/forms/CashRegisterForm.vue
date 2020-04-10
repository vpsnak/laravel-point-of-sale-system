<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit"
  >
    <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
      <ValidationProvider
        rules="required|max:100"
        v-slot="{ errors }"
        name="Name"
      >
        <v-text-field
          v-model="formFields.name"
          :readonly="$props.readonly"
          label="Name"
          :disabled="loading"
          :error-messages="errors"
        />
      </ValidationProvider>
      <ValidationProvider
        rules="required|max:3"
        v-slot="{ errors }"
        name="Stores"
      >
        <v-select
          :readonly="$props.readonly"
          v-model="formFields.store_id"
          label="Stores"
          :items="stores"
          item-text="name"
          item-value="id"
          :loading="storesLoading"
          :disabled="loading"
          :error-messages="errors"
        />
      </ValidationProvider>
      <ValidationProvider
        rules="required|max:100"
        v-slot="{ errors }"
        name="Barcode"
      >
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.barcode"
          label="Barcode"
          :disabled="loading"
          :error-messages="errors"
        />
      </ValidationProvider>
      <ValidationProvider
        rules="required|max:45"
        v-slot="{ errors }"
        name="Pos Terminal IP"
      >
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.pos_terminal_ip"
          label="Pos Terminal IP"
          :disabled="loading"
          :error-messages="errors"
        />
      </ValidationProvider>
      <ValidationProvider
        rules="required|max:100"
        v-slot="{ errors }"
        name="Pos terminal port"
      >
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.pos_terminal_port"
          label="Pos terminal port"
          :disabled="loading"
          :error-messages="errors"
        />
      </ValidationProvider>
      <v-checkbox
        v-model="formFields.active"
        :readonly="$props.readonly"
        :disabled="loading"
        label="Active"
      />
    </v-container>
    <v-container v-if="!$props.readonly">
      <v-row justify="center">
        <v-btn
          type="submit"
          :loading="submitLoading"
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
      storesLoading: false,
      submitLoading: false,

      formFields: {
        name: null,
        store_id: null,
        active: true,
        barcode: null,
        pos_terminal_ip: null,
        pos_terminal_port: null
      },
      stores: []
    };
  },

  mounted() {
    this.getAllStores();
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
    }
  },

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    loading() {
      if (this.storesLoading || this.submitLoading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.submitLoading = true;
      const payload = {
        method: this.formFields.id ? "patch" : "post",
        url: this.formFields.id
          ? "cash-registers/update"
          : "cash-registers/create",
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
          this.submitLoading = false;
        });
    },
    getAllStores() {
      this.storesLoading = true;
      const payload = {
        method: "get",
        url: "stores"
      };

      this.request(payload)
        .then(response => {
          this.stores = response.data;
        })
        .finally(() => {
          this.storesLoading = false;
        });
    }
  }
};
</script>
