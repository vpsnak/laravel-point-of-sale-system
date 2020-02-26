<template>
  <v-row>
    <v-col cols="6">
      <v-btn block @click="back()" :disabled="saveLoading">
        <v-icon left>mdi-arrow-left</v-icon>
        back
      </v-btn>
    </v-col>

    <v-col :cols="6">
      <v-btn
        block
        :disabled="!enableSave || saveLoading"
        :loading="saveLoading"
        @click="confirmationDialog()"
      >
        <v-icon left>mdi-content-save-outline</v-icon>
        save
      </v-btn>
    </v-col>
  </v-row>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../../plugins/event-bus";

export default {
  props: {
    url: String
  },

  mounted() {
    EventBus.$on("order-save-confirmation", event => {
      if (event.payload) {
        this.save();
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("order-save-confirmation");
  },

  data() {
    return {
      saveLoading: false
    };
  },

  computed: {
    ...mapState("cart", ["cart_products"]),

    totalProducts() {
      return _.size(this.cart_products) ? false : true;
    },
    enableSave() {
      if (_.size(this.cart_products)) {
        return true;
      } else {
        return false;
      }
    }
  },
  methods: {
    ...mapMutations("dialog", ["setDialog"]),
    ...mapActions("cart", ["submitOrder"]),

    confirmationDialog() {
      this.setDialog({
        show: true,
        action: "confirmation",
        icon: "mdi-content-save-outline",
        title: "Save changes?",
        content: "This action is <b>irreversible</b>. Do you want to continue?",
        action: "confirmation",
        persistent: true,
        cancelBtnTxt: "No",
        confirmationBtnTxt: "Yes",
        eventChannel: "order-save-confirmation"
      });
    },
    save() {
      this.saveLoading = true;
      this.submitOrder(this.$props.url)
        .then(() => {
          this.back();
        })
        .finally(() => {
          this.saveLoading = false;
        });
    },
    back() {
      this.$router.go(-1);
    }
  }
};
</script>