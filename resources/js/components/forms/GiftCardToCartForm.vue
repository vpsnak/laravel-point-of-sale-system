<template>
<ValidationObserver slim v-slot={invalid}>
  <v-container fluid>
    <v-row>
      <v-col :cols="12">
        <v-text-field
            v-model="keyword"
            @click:prepend-inner="getGiftCard()"
            @keyup.enter="getGiftCard()"
            label="Search by code or name"
            prepend-inner-icon="mdi-magnify"
            clearable
            single-line
            outlined
            dense
          />
      </v-col>
      <v-col :cols="12">
        <v-data-table
          v-model="selectedGiftcard"
          dense
          :headers="headers.giftcardToCart"
          :items="giftcards"
          :loading="datatableLoading"
          single-select
          show-select
          class="elevation-3"
          hide-default-footer
          disable-filtering
          disable-pagination
          disable-sort
          height="150px"
        >
          <template v-slot:item.price="{ item }">
            <h4>{{ parsePrice(item.price).toFormat() }}</h4>
          </template>
        </v-data-table>
      </v-col>
    </v-row>

      <v-row justify="center" v-if="isEnabled">
        <v-col :cols="4">
          <ValidationProvider
            rules="required|between:0.01,10000"
            v-slot="{ errors, valid }"
            name="Recharge amount"
          >
            <v-text-field
              v-model="price"
              type="number"
              :disabled="!selectedGiftcard[0]"
              @keyup.enter="valid ? submit() : null"
              @click:clear="price = original_price"
              :error-messages="errors"
              clearable
              label="Recharge amount"
              :hint="`Original amount: ${original_price.toFormat()}`"
              prefix="$"
              outlined
              dense
            />
          </ValidationProvider>
        </v-col>
      </v-row>
    <v-row justify="center">
      <v-btn
        text
        outlined
        v-text="'Add to cart'"
        @click.stop="submit()"
        :disabled="invalid || !selectedGiftcard[0]"
        color="primary"
      />
    </v-row>
  </v-container>
  </ValidationObserver>
</template>

<script>
import { mapState, mapActions } from "vuex";

export default {
  data() {
    return {
      original_price: {},
      recharge_price: {},
      recharge_price_amount: null,

      datatableLoading: false,
      keyword: null,
      giftcards: [],
      selectedGiftcard: []
    };
  },

  mounted() {
    this.$root.$on("barcodeScan", code => {
      this.keyword = code;
      this.getGiftCard();
    });
  },

  beforeDestroy() {
    this.$off("submit");
  },

  computed: {
    ...mapState("datatable", ["headers"]),

    isEnabled() {
      if (this.selectedGiftcard[0] && this.selectedGiftcard[0].enabled_at) {
        return true;
      } else {
        return false;
      }
    },
    price: {
      get() {
        return this.recharge_price_amount;
      },
      set(value) {
        this.recharge_price_amount = value;
        this.recharge_price = this.parsePrice(
          Math.round(value * 10000) / 100
        ).toJSON();
      }
    }
  },

  watch: {
    selectedGiftcard(value) {
      if (value[0]) {
        this.original_price = this.parsePrice(value[0].price);
      } else {
        this.price_amount = null;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),
    ...mapActions("cart", ["addProduct"]),

    getGiftCard() {
      if (this.keyword.length) {
        this.datatableLoading = true;
        const payload = {
          method: "post",
          url: "giftcards/search",
          data: { keyword: this.keyword }
        };
        this.request(payload)
          .then(response => {
            this.giftcards = response.data;
            if (this.giftcards.length === 1) {
              this.selectedGiftcard[0] = this.giftcards[0];
            }
          })
          .finally(() => {
            this.datatableLoading = false;
          });
      }
    },
    submit() {
      this.selectedGiftcard[0].id = this.selectedGiftcard[0].sku =
          this.selectedGiftcard[0].code;
      if (this.isEnabled) {
        this.selectedGiftcard[0].price = this.recharge_price;
      }
      this.addProduct(this.selectedGiftcard[0]);
      this.$emit("submit", true);
    }
  }
};
</script>
