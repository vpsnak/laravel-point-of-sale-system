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
          rules="required|max:3"
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
        <ValidationProvider
          rules="required|max:255"
          v-slot="{ errors, valid }"
          name="Barcode"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.barcode"
            label="Barcode"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|max:45"
          v-slot="{ errors, valid }"
          name="Pos Terminal IP"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.pos_terminal_ip"
            label="Pos Terminal IP"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|max:255"
          v-slot="{ errors, valid }"
          name="Pos terminal port"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.pos_terminal_port"
            label="Pos terminal port"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.active"
          label="Active"
        ></v-checkbox>
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
          url: "cash-registers/update",
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
          url: "cash-registers/create",
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
    },
    getAllStores() {
      this.loading = true;
      this.request({
        method: "get",
        url: "stores"
      })
        .then(response => {
          this.stores = response.data;
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
