<template>
  <v-app id="app">
    <notification />

    <interactiveDialog />

    <checkoutDialog v-if="checkoutDialog" />

    <transition name="fade" mode="out-in">
      <v-content>
        <router-view class="view side-menu" name="side_menu" />
        <router-view class="view top-menu" name="top_menu" />
        <router-view class="view default" />
      </v-content>
    </transition>
  </v-app>
</template>

<script>
import { mapGetters, mapMutations, mapState } from "vuex";

export default {
  created() {
    if (this.auth) {
      this.$router.replace({ name: "landingPage" });
    } else {
      if (this.$route.name !== "login") {
        this.$router.replace({ name: "login" });
      }
    }
  },
  computed: {
    ...mapState(["user"]),
    ...mapState("cart", ["checkoutDialog"]),
    ...mapGetters(["auth"]),
  },

  watch: {
    user: {
      deep: true,
      immediate: true,
      handler(value) {
        this.applyUserTheme();
      },
    },
  },

  methods: {
    ...mapMutations(["logout"]),

    applyUserTheme() {
      if (_.has(this.user, "settings")) {
        const theme_dark = _.find(this.user.settings, {
          key: "theme_dark",
        });

        if (_.has(theme_dark, "value") && theme_dark.value === "0") {
          this.$vuetify.theme.dark = false;
        } else {
          this.$vuetify.theme.dark = true;
        }
      }
    },
  },
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
