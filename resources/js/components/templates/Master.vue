<template>
  <v-app id="app">
    <notification />
    <interactiveDialog />
    <checkoutDialog />

    <v-content ref="main">
      <router-view class="view side-menu" name="side_menu" />
      <router-view class="view top-menu" name="top_menu" />
      <perfect-scrollbar ref="scroll" :style="`height: ${inner_height}px;`">
        <router-view class="default" name="default" />
      </perfect-scrollbar>
    </v-content>
  </v-app>
</template>

<script>
import { mapState, mapGetters, mapMutations, mapActions } from "vuex";
import { EventBus } from "../../plugins/eventBus";

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

  mounted() {
    this.setInnerHeight(
      window.innerHeight - this.$refs.main.$el.style.paddingTop.slice(0, -2)
    );

    window.addEventListener("resize", () => {
      this.setInnerHeight(
        window.innerHeight - this.$refs.main.$el.style.paddingTop.slice(0, -2)
      );
      this.$nextTick(() => {
        this.$refs.scroll.update();
      });
    });
  },

  beforeDestroy() {
    EventBus.$off("inner-height");
    window.removeEventListener("resize");
  },

  computed: {
    ...mapState("dialog", ["interactive_dialog"]),
    ...mapState(["user"]),
    ...mapState("config", ["inner_height"]),
    ...mapGetters(["auth"]),
  },

  watch: {
    $route() {
      this.$refs.scroll.$el.scrollTop = 0;
    },
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
    ...mapMutations("config", ["setInnerHeight"]),
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
html {
  overflow-y: hidden;
}

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
