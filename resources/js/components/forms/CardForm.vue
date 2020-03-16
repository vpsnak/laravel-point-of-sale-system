<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider
          rules="required|max:255"
          v-slot="{ errors, valid }"
          name="Title"
        >
          <v-text-field
            :readonly="$props.readonly"
            v-model="formFields.title"
            label="Title"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-text-field>
        </ValidationProvider>
        <ValidationProvider
          rules="required"
          v-slot="{ errors, valid }"
          name="Content"
        >
          <v-textarea
            :readonly="$props.readonly"
            v-model="formFields.content"
            label="Content"
            :disabled="loading"
            :error-messages="errors"
            :success="valid"
          ></v-textarea>
        </ValidationProvider>
        <ValidationProvider
          v-if="!order_checkbox"
          :rules="{ required: { allowFalse: false } }"
          v-slot="{ errors, valid }"
          name="Products"
        >
          <v-switch
            @mouseup="formFields.cardable_id = null"
            v-if="!order_checkbox"
            :disabled="loading"
            :readonly="$props.readonly"
            v-model="product_checkbox"
            label="Products"
            :error-messages="errors"
            :success="valid"
          ></v-switch>
        </ValidationProvider>

        <ValidationProvider
          v-if="product_checkbox"
          rules="required"
          v-slot="{ errors, valid }"
          name="Products"
        >
          <v-select
            :readonly="$props.readonly"
            v-model="formFields.cardable_id"
            :items="products"
            label="Products"
            :disabled="loading"
            item-text="name"
            item-value="id"
            :error-messages="errors"
            :success="valid"
          ></v-select>
        </ValidationProvider>

        <ValidationProvider
          v-if="!product_checkbox"
          :rules="{ required: { allowFalse: false } }"
          v-slot="{ errors, valid }"
          name="Checkbox"
        >
          <v-switch
            @mouseup="formFields.cardable_id = null"
            :disabled="loading"
            v-if="!product_checkbox"
            :readonly="$props.readonly"
            v-model="order_checkbox"
            label="Orders"
            :error-messages="errors"
            :success="valid"
          ></v-switch>
        </ValidationProvider>

        <ValidationProvider
          v-if="order_checkbox"
          rules="required"
          v-slot="{ errors, valid }"
          name="Orders"
        >
          <v-select
            :readonly="$props.readonly"
            v-model="formFields.cardable_id"
            :items="orders"
            label="Orders"
            :disabled="loading"
            item-text="method"
            item-value="id"
            :error-messages="errors"
            :success="valid"
          ></v-select>
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
      products: [],
      orders: [],
      product_checkbox: false,
      order_checkbox: false,
      loading: false,
      defaultValues: {},
      formFields: {
        title: null,
        content: null,
        cardable_id: null,
        cardable_type: null
      }
    };
  },
  mounted() {
    this.getAllProducts();
    this.getAllOrders();
    this.defaultValues = { ...this.formFields };
    if (this.$props.model) {
      this.formFields = {
        ...this.$props.model
      };
      if (this.formFields.cardable_type == "product") {
        this.product_checkbox = true;
      } else if (this.formFields.cardable_type == "order") {
        this.order_checkbox = true;
      }
    }
  },
  methods: {
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;

      if (this.product_checkbox) {
        this.formFields.cardable_type = "product";
      } else if (this.order_checkbox) {
        this.formFields.cardable_type = "order";
      }

      if (this.$props.model) {
        this.request({
          method: "patch",
          url: "cards/update",
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
          url: "cards/create",
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
    getAllProducts() {
      this.loading = true;
      this.request({
        method: "get",
        url: "products"
      })
        .then(response => {
          this.products = response.data;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllOrders() {
      this.loading = true;
      this.request({
        method: "get",
        url: "orders"
      })
        .then(response => {
          this.orders = response.data;
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
