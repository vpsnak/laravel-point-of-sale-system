<template>
  <ValidationObserver slim v-slot="{ invalid }">
    <v-container fluid>
      <v-row v-if="!$props.model">
        <v-col :cols="12">
          <v-text-field
            v-model="keyword"
            @click:prepend-inner="getGiftCards()"
            @keyup.enter="getGiftCards()"
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
            fixed-header
          >
            <template v-slot:item.price="{ item }">
              <h4>{{ parsePrice(item.price).toFormat() }}</h4>
            </template>
          </v-data-table>
        </v-col>
        <v-col v-show="giftcards.length" :cols="12">
          <v-pagination
            v-model="page"
            :length="pageCount"
            @input="getGiftCards()"
          />
        </v-col>
      </v-row>

      <v-row justify="center" v-if="isEnabled || $props.model">
        <v-col :cols="4">
          <ValidationProvider
            rules="required|between:0.01,10000"
            v-slot="{ errors, valid }"
            name="Recharge amount"
          >
            <v-text-field
              v-model="rechargePrice"
              type="number"
              :disabled="!selectedGiftcard[0]"
              @keyup.enter="valid ? submit() : null"
              :error-messages="errors"
              clearable
              label="Recharge amount"
              :hint="
                `Original amount: ${parsePrice(
                  selectedGiftcard[0].original_price
                ).toFormat()}`
              "
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
  props: {
    model: Object
  },

  data() {
    return {
      pageCount: null,
      page: 1,

      recharge_price: {},
      recharge_price_amount: null,

      datatableLoading: false,
      keyword: null,
      giftcards: [],
      selectedGiftcard: []
    };
  },

  created() {
    if (this.$props.model) {
      this.selectedGiftcard.push(this.$props.model);
    }
  },

  mounted() {
    if (!this.$props.model) {
      this.$root.$on("barcodeScan", code => {
        this.keyword = code;
        this.getGiftCards();
      });
    }
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
    rechargePrice: {
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

    getGiftCards() {
      if (this.keyword.length) {
        this.datatableLoading = true;
        const payload = {
          method: "post",
          url: `giftcards/search?page=${this.page}`,
          data: { keyword: this.keyword, items: 5 }
        };
        this.request(payload)
          .then(response => {
            this.page = response.current_page;
            this.pageCount = response.last_page;
            this.giftcards = response.data;

            if (this.giftcards.length === 1) {
              this.selectedGiftcard.push(this.giftcards[0]);
            }
          })
          .finally(() => {
            this.datatableLoading = false;
          });
      }
    },
    submit() {
      if (this.isEnabled) {
        this.selectedGiftcard[0].price = this.recharge_price;
      }
      this.addProduct(this.selectedGiftcard[0]);
      if (this.$props.model) {
        this.$router.push({ name: "sale" });
      }
      this.$emit("submit", true);
    }
  }
};
</script>
