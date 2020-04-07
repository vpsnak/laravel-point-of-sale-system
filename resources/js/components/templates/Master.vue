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
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";

export default {
  created() {
    this.initCookies().then(() => {
      if (this.auth) {
        this.$router.replace({ name: "landingPage" });
      } else {
        if (this.$route.name !== "login") {
          this.$router.replace({ name: "login" });
        }
      }
    });
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
        this.applyUserTheme(value);
      },
    },
  },

  methods: {
    ...mapMutations(["logout"]),
    ...mapActions("config", ["initCookies"]),

    applyUserTheme(value) {
      if (_.has(value, "settings")) {
        const theme_dark = _.find(value.settings, {
          key: "theme_dark",
        });

        if (_.has(theme_dark, "value")) {
          this.$vuetify.theme.dark = theme_dark.value === "1" ? true : false;
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
