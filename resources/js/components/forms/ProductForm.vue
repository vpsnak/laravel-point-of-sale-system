<template>
  <ValidationObserver
    v-slot="{ invalid }"
    tag="v-form"
    @submit.prevent="submit()"
  >
    <v-container class="overflow-y-auto" style="max-height: 60vh">
      <v-row justify="space-around">
        <v-col :cols="12">
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
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
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
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <ValidationProvider rules="max:255" v-slot="{ errors }" name="Url">
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.url"
              label="Url"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <ValidationProvider
            rules="max:255"
            v-slot="{ errors }"
            name="Photo url"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.photo_url"
              label="Photo url"
              :error-messages="errors"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <ValidationProvider
            rules="max:30000"
            v-slot="{ errors }"
            name="Description"
          >
            <v-text-field
              :readonly="$props.readonly"
              v-model="formFields.description"
              label="Description"
              :error-messages="errors"
              :disabled="loading"
            />
          </ValidationProvider>
        </v-col>
        <v-col :cols="12">
          <ValidationProvider rules="required" v-slot="{ errors }" name="Price">
            <v-text-field
              :readonly="$props.readonly"
              type="number"
              v-model="price"
              label="Price"
              prefix="$"
              :error-messages="errors"
              :disabled="loading"
            />
          </ValidationProvider>
        </v-col>

        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.is_price_editable"
          label="Editable price"
          :disabled="loading"
        />

        <v-checkbox
          :readonly="$props.readonly"
          v-model="formFields.is_discountable"
          label="Discountable"
          :disabled="loading"
        />

        <v-col cols="12">
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
          />
        </v-col>
      </v-row>
      <v-row v-if="formFields.stores">
        <v-col :cols="12">
          <v-card outlined>
            <v-simple-table fixed-header>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">
                      <span v-text="'Store name'" />
                    </th>
                    <th class="text-left">
                      <span v-text="'Qty'" />
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="store in formFields.stores" :key="store.id">
                    <td>
                      <span v-text="store.name" />
                    </td>
                    <td>
                      <v-text-field
                        v-model="store.pivot.qty"
                        type="number"
                        :disabled="loading"
                        :readonly="$props.readonly"
                        style="width:60px;"
                      />
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <v-container v-if="!$props.readonly">
      <v-row justify="center">
        <v-btn
          class="mr-4"
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
        stores: null
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

  beforeDestroy() {
    this.$off("submit");
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
      this.submitLoading = true;
      const payload = {
        method: this.formFields.id ? "patch" : "post",
        url: this.formFields.id ? "products/update" : "products/create",
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
  }
};
</script>
