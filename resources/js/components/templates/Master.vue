<template>
  <v-app id="app">
    <notification />

    <interactiveDialog v-if="showComponents"></interactiveDialog>

    <checkoutDialog v-if="showComponents && checkoutDialog" />

    <sideMenu v-if="showComponents" />

    <topMenu v-if="showComponents" />

    <v-content>
      <transition name="fade">
        <router-view></router-view>
      </transition>
    </v-content>
  </v-app>
</template>

<script>
import { mapGetters, mapMutations, mapState } from "vuex";

export default {
  mounted() {
    if (this.auth && this.app_load <= 100) {
      this.$router.push({ name: "landingPage" });
    }
  },
  computed: {
    ...mapGetters(["auth", "role"]),
    ...mapState("config", ["app_load"]),

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
