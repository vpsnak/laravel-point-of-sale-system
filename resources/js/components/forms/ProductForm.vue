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
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required|max:191"
          v-slot="{ errors, valid }"
          name="Sku"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.sku"
            label="Sku"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="max:191"
          v-slot="{ errors, valid }"
          name="Url"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.url"
            label="Url"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="max:191"
          v-slot="{ errors, valid }"
          name="Photo url"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.photo_url"
            label="Photo url"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>

        <ValidationProvider
          rules="max:65535"
          v-slot="{ errors, valid }"
          name="Description"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.description"
            label="Description"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>

        <ValidationProvider
          :rules="{
            required: true,
            regex: /^[\d]{1,8}(\.[\d]{1,4})?$/g
          }"
          v-slot="{ errors, valid }"
          name="Final price"
        >
          <v-text-field
            :readonly="$props.readonly"
            type="number"
            v-model="formFields.final_price"
            label="Final price"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>

        <v-row justify="space-around">
          <v-checkbox
            :readonly="$props.readonly"
            v-model="formFields.editable_price"
            label="Editable  price"
          ></v-checkbox>
        </v-row>

        <v-select
          :readonly="$props.readonly"
          :loading="loading"
          v-model="formFields.categories"
          :items="categories"
          chips
          label="Categories"
          multiple
          item-text="name"
          item-value="id"
          clearable
        ></v-select>

        <v-row v-if="formFields.stores">
          <!--			mporeis na valeis item, index gia na exeis access se poia epanalipsi eisai => na ksereis pio entry na peirakseis-->
          <v-col :key="store.id" v-for="(store, index) in formFields.stores">
            <v-card>
              <v-card-title class="blue-grey pa-0" @click.stop>
                <h6 class="px-2">{{ store.name }}</h6>
              </v-card-title>
              <ValidationProvider
                rules="max:10"
                v-slot="{ errors, valid }"
                name="Qty"
              >
                <v-text-field
                  :readonly="$props.readonly"
                  type="number"
                  label="Qty"
                  :value="formFields.stores[index].pivot.qty"
                  :error-messages="errors"
                  :success="valid"
                  v-model="formFields.stores[index].pivot.qty"
                  required
                ></v-text-field>
              </ValidationProvider>
            </v-card>
          </v-col>
        </v-row>
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
      categories: [],
      selectedCategories: [],
      stores: [],
      defaultValues: {},
      formFields: {
        name: "",
        sku: null,
        photo_url: null,
        final_price: null,
        url: null,
        description: null,
        editable_price: false,
        categories: [],
        stores: []
      }
    };
  },
  mounted() {
    this.getAllCategories();
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
      if (
        this.formFields.categories != null &&
        typeof this.formFields.categories[0] === "object"
      ) {
        let category_ids = [];
        for (const category of this.formFields.categories) {
          category_ids.push(category.id);
        }
        this.formFields.categories = category_ids;
      }

      this.loading = true;

      if (this.$props.model) {
        this.request({
          method: "patch",
          url: "products/update",
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
          url: "products/create",
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
    getAllCategories() {
      this.loading = true;
      this.request({
        method: "get",
        url: "categories"
      })
        .then(response => {
          this.categories = response.data;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllStores() {
      this.request({
        method: "get",
        url: "stores"
      }).then(response => {
        // india gia na exoume default value sto qty twn stores
        // @TODO fix object assign on product edit
        response = response.data.map(item => {
          return (item = { ...item, ...{ pivot: { qty: 0 } } });
        });
        if (
          this.formFields.stores === undefined ||
          this.formFields.stores.length == 0
        ) {
          this.formFields.stores = response;
        }
        // reset default values after getting and setting the stores
        this.defaultValues = { ...this.formFields };
      });
    }
  },
  beforeDestroy() {
    this.$off("submit");
  }
};
</script>
