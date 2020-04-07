<template>
  <v-container v-if="this.app_load <= 100" class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col :cols="12" :sm="8" :md="4">
        <v-img
          contain
          src="https://www.plantshed.com/skin/frontend/plantshed/default/images/ps-logo.svg"
        />
      </v-col>
      <v-col :cols="12" align="center" justify="center">
        <v-progress-circular
          rotate="270"
          :color="color"
          :size="150"
          :value="error_txt ? 100 : loadPercent"
          :width="18"
        >
          <b v-text="error_txt ? error_txt : `${loadPercent}%`" />
        </v-progress-circular>
      </v-col>
    </v-row>
    <v-row v-if="verbose && init_info.length">
      <v-col :cols="12">
        <transition-group
          name="staggered-fade"
          tag="table"
          v-bind:css="false"
          @before-enter="beforeEnter"
          @enter="enter"
        >
          <tr
            v-for="(item, index) in init_info"
            :key="item.action"
            :data-index="index"
          >
            <td v-text="item.action" />
            <td :class="`${item.status}--text`" v-text="item.message" />
          </tr>
        </transition-group>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
export default {
  mounted() {
    if (this.loadPercent !== 101) {
      this.init();
    } else {
      this.redirect();
    }
  },

  data() {
    return {
      error_txt: "",
    };
  },

  computed: {
    ...mapState(["token"]),
    ...mapState("config", ["app_load", "verbose", "init_info"]),

    color() {
      if (this.error_txt) {
        return "error";
      } else {
        if (this.$vuetify.theme.dark) {
          return "white";
        } else {
          return "black";
        }
      }
    },
    loadPercent: {
      get() {
        return this.app_load;
      },
      set(value) {
        this.addLoadPercent(value);

        if (this.app_load === 100) {
          this.addLoadPercent(1);
          this.redirect();
        }
      },
    },
  },
  methods: {
    ...mapMutations(["logout"]),
    ...mapMutations("config", ["addLoadPercent"]),
    ...mapMutations("cart", ["resetState"]),
    ...mapMutations("dialog", ["resetDialog"]),
    ...mapActions("config", [
      "initWebSockets",
      "initChannels",
      "getMasEnv",
      "getMenuItems",
      "retrieveCashRegister",
    ]),

    init() {
      this.resetAppState();

      this.getMenuItems()
        .then(() => {
          this.loadPercent = 15;
        })
        .catch((error) => {
          this.setError(error);
        });
      this.initWebSockets()
        .then(() => {
          this.loadPercent = 15;

          this.initChannels()
            .then(() => {
              this.loadPercent = 15;
            })
            .catch((error) => {
              this.setError(error);
            });
        })
        .catch((error) => {
          if (process.env.NODE_ENV === "development") {
            this.loadPercent = 30;
          } else {
            this.setError(error);
          }
        });
      this.getMasEnv()
        .then(() => {
          this.loadPercent = 15;
        })
        .catch((error) => {
          this.setError(error);
        });
      this.retrieveCashRegister()
        .then(() => {
          this.loadPercent = 30;
        })
        .catch((error) => {
          this.setError(error);
        });
    },
    redirect() {
      if (this.$route.name !== "dashboard") {
        this.$router.replace({ name: "dashboard" });
      }
    },
    resetAppState() {
      this.resetState();
      this.resetDialog();
      this.loadPercent = 10;
    },
    setError(error) {
      this.loadPercent = 0;
      this.error_txt = "Error";
      console.error(error);
    },

    // animations
    beforeEnter(el) {
      el.style.opacity = 0;
      el.style.height = 0;
    },
    enter(el, done) {
      const delay = el.dataset.index * 150;
      setTimeout(() => {
        Velocity(el, { opacity: 1, height: "1.6em" }, { complete: done });
      }, delay);
    },
    leave(el, done) {
      const delay = el.dataset.index * 150;
      setTimeout(() => {
        Velocity(el, { opacity: 0, height: 0 }, { complete: done });
      }, delay);
    },
  },
};
</script>
