<template>
  <v-app id="app">
    <notification />

    <interactiveDialog v-if="showComponents"></interactiveDialog>

    <checkoutDialog v-if="showComponents && checkoutDialog" />

    <transition name="fade" mode="out-in">
      <v-content>
        <router-view class="view side-menu" name="side_menu"></router-view>
        <router-view class="view top-menu" name="top_menu"></router-view>
        <router-view class="view default"></router-view>
      </v-content>
    </transition>
  </v-app>
</template>

<script>
import { mapGetters, mapMutations, mapState } from "vuex";

export default {
  created() {
    if (this.auth && this.app_load <= 100) {
      this.$router.replace({ name: "landingPage" });
    }
  },
  computed: {
    ...mapGetters(["auth", "role"]),
    ...mapState("config", ["app_load"]),
    ...mapState("cart", ["checkoutDialog"]),

    showComponents() {
      if (this.app_load > 100 && this.auth) {
        return true;
      } else {
        return false;
      }
    }
  },
  methods: {
    ...mapMutations(["logout"])
  }
};
</script>

<style>
a {
  text-decoration: none;
}

input[type="number"] {
  -moz-appearance: textfield;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
}
</style>
