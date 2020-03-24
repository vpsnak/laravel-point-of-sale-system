<template>
  <ValidationObserver
    v-slot="{ invalid }"
    @submit.prevent="submit()"
    tag="v-form"
  >
    <v-container class="overflow-y-auto" style="max-height: 60vh">
      <ValidationProvider
        rules="required|max:100"
        v-slot="{ errors }"
        name="Name"
      >
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.name"
          label="Name"
          :error-messages="errors"
          :color="valid ? 'primary' : ''"
        ></v-text-field>
      </ValidationProvider>
      <ValidationProvider
        rules="required|max:100"
        v-slot="{ errors }"
        name="Sku"
      >
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.sku"
          label="Sku"
          :error-messages="errors"
          :color="valid ? 'primary' : ''"
        ></v-text-field>
      </ValidationProvider>
      <ValidationProvider rules="max:100" v-slot="{ errors }" name="Url">
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.url"
          label="Url"
          :error-messages="errors"
          :color="valid ? 'primary' : ''"
        ></v-text-field>
      </ValidationProvider>
      <ValidationProvider rules="max:100" v-slot="{ errors }" name="Photo url">
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.photo_url"
          label="Photo url"
          :error-messages="errors"
          :color="valid ? 'primary' : ''"
        ></v-text-field>
      </ValidationProvider>

      <ValidationProvider
        rules="max:65535"
        v-slot="{ errors }"
        name="Description"
      >
        <v-text-field
          :readonly="$props.readonly"
          v-model="formFields.description"
          label="Description"
          :error-messages="errors"
          :color="valid ? 'primary' : ''"
          :disabled="loading"
        ></v-text-field>
      </ValidationProvider>

      <ValidationProvider rules="required" v-slot="{ errors }" name="Price">
        <v-text-field
          :readonly="$props.readonly"
          type="number"
          v-model="price"
          label="Price"
          prefix="$"
          :error-messages="errors"
          :color="valid ? 'primary' : ''"
          :disabled="loading"
        ></v-text-field>
      </ValidationProvider>

      <v-row justify="space-around">
        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.is_price_editable"
          label="Editable price"
          :disabled="loading"
        ></v-checkbox>

        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.is_discountable"
          label="Discountable"
          :disabled="loading"
        ></v-checkbox>
      </v-row>

      <v-select
        :readonly="$props.readonly"
        :loading="categoriesLoading"
        v-model="formFields.categories"
        :items="categories"
        chips
        label="Categories"
        multiple
        item-text="name"
        item-value="id"
        :disabled="loading"
        clearable
      ></v-select>

      <v-row v-if="formFields.stores">
        <v-col :key="store.id" v-for="store in formFields.stores">
          <v-card>
            <v-card-title class="blue-grey pa-0" @click.stop>
              <h6 class="px-2">{{ store.name }}</h6>
            </v-card-title>
            <ValidationProvider rules="max:10" v-slot="{ errors }" name="Qty">
              <v-text-field
                :readonly="$props.readonly"
                type="number"
                label="Qty"
                :error-messages="errors"
                v-model="store.pivot.qty"
                required
                :color="valid ? 'primary' : ''"
                :disabled="loading"
              ></v-text-field>
            </ValidationProvider>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <v-row v-if="!$props.readonly">
        <v-col :cols="12" align="center" justify="center">
          <v-btn
            class="mr-4"
            type="submit"
            :loading="submitLoading"
            :disabled="invalid || loading"
            color="primary"
            >submit
          </v-btn>
        </v-col>
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
      price_amount: null,
      categoriesLoading: false,
      submitLoading: false,

      categories: [],
      selectedCategories: [],
      formFields: {
        name: "",
        sku: null,
        photo_url: null,
        price: null,
        url: null,
        description: null,
        is_price_editable: true,
        is_discountable: true,
        categories: [],
        stores: []
      }
    };
  },

  mounted() {
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
      this.price_amount = this.parsePrice(this.formFields.price).toFormat(
        "0.00"
      );
    }
    this.getAllCategories();
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
    },
    loading() {
      if (this.submitLoading || this.categoriesLoading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      console.log(this.formFields.categories);
      this.submitLoading = true;

      const payload = {
        method: "post",
        url: "products/create",
        data: { ...this.formFields }
      };
      if (this.$props.model) {
        payload.method = "patch";
        payload.url = "products/update";
      }

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
    getAllCategories() {
      this.categoriesLoading = true;
      const payload = {
        method: "get",
        url: "categories"
      };
      this.request(payload)
        .then(response => {
          this.categories = response.data;
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.categoriesLoading = false;
        });
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
