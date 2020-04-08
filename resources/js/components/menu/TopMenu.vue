<template>
  <v-app-bar app clipped-left>
    <v-app-bar-nav-icon @click.stop="menuVisibility = !menuVisibility" />
    <v-toolbar-title v-text="app_name" />

    <v-spacer />

    <v-toolbar-title>
      <span v-text="'Environment: '" />
      <i :class="txtColor(app_env)" v-text="app_env" />
    </v-toolbar-title>

    <v-spacer />

    <v-toolbar-title>
      <span v-text="'MAS Env: '" />
      <i :class="txtColor(mas_env)" @click="clicks++" v-text="mas_env" />
    </v-toolbar-title>

    <v-spacer />

    <v-chip
      v-if="!cashRegister"
      text
      @click="$router.push({ name: 'openCashRegister' })"
      label
    >
      <span v-text="'Select cash register'" />
    </v-chip>
    <v-menu
      v-if="cashRegister"
      left
      bottom
      offset-y
      transition="scale-transition"
      origin="top center"
    >
      <template v-slot:activator="{ on }">
        <v-chip v-on="on" class="ma-2" color="primary" label>
          <v-icon left v-text="'mdi-cash-register'" />
          <span v-text="cashRegister.name" />
        </v-chip>
      </template>
      <v-list dense>
        <v-list-item inactive two-line @click.stop :ripple="false">
          <v-list-item-avatar color="primary">
            <v-icon dark v-text="'mdi-cash-register'" />
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="store_name" />
            </v-list-item-title>
            <v-list-item-subtitle>
              <span v-text="cashRegister.name" />
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>

        <v-divider />

        <v-list-item @click="checkCashRegisterDialog">
          <v-list-item-icon>
            <v-icon v-text="'mdi-alpha-x-circle'" />
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="'View X report'" />
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list-item @click.stop="cashRegisterLogout()">
          <v-list-item-icon>
            <v-icon v-text="'mdi-logout'" />
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="'Logout'" />
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-divider />

        <v-list-item @click.stop="closeCashRegisterDialog()">
          <v-list-item-icon>
            <v-icon v-text="'mdi-alpha-z-circle'" />
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="'Close and generate Z report'" />
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-menu>

    <v-menu
      left
      bottom
      offset-y
      transition="scale-transition"
      origin="top right"
    >
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" fab small color="blue-grey darken-2">
          <span v-text="nameInitials" style="font-size:18px; color:white;" />
        </v-btn>
      </template>
      <v-list dense>
        <v-list-item inactive two-line @click.stop :ripple="false">
          <v-list-item-avatar color="blue-grey darken-2">
            <span v-text="nameInitials" style="font-size:18px; color:white;" />
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="user.name" />
            </v-list-item-title>
            <v-list-item-subtitle>
              <span v-text="user.email" />
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>

        <v-divider />

        <v-list-item
          v-for="menu_item in top_menu"
          :key="menu_item.id"
          :to="menuLink(menu_item)"
          @click.stop="menuAction(menu_item)"
          exact
        >
          <v-list-item-icon>
            <v-icon v-text="menu_item.icon" />
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="menu_item.title" />
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-divider />

        <v-list-item :ripple="false" @click.stop dense>
          <v-list-item-icon>
            <v-icon v-text="'mdi-weather-night'" />
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              <span v-text="'Dark theme'" />
            </v-list-item-title>
          </v-list-item-content>
          <v-list-item-action>
            <v-switch v-model="darkMode" :loading="darkModeLoading" />
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { EventBus } from "../../plugins/eventBus";

export default {
  mounted() {
    EventBus.$on("top-menu-generate-z", event => {
      if (event && _.has(event, "payload.response.report")) {
        this.displayZDialog(event.payload.response.report);
      }
    });
  },

  beforeDestroy() {
    EventBus.$off("top-menu-generate-z");
  },

  data() {
    return {
      clicks: 0,
      darkModeLoading: false
    };
  },

  watch: {
    clicks(value) {
      if (value === 10) {
        this.clicks = 0;
        const payload = this.mas_env === "production" ? "test" : "production";
        this.setMasEnv(payload);
      }
    }
  },

  computed: {
    ...mapState("menu", ["top_menu", "visibility", "store_name"]),
    ...mapState("config", ["app_env", "app_name", "mas_env"]),
    ...mapState(["user", "cashRegister"]),

    darkMode: {
      get() {
        return this.$vuetify.theme.dark;
      },
      set(value) {
        this.$vuetify.theme.dark = value;

        this.darkModeLoading = true;
        const payload = {
          method: "patch",
          url: "settings/theme-dark",
          data: { value: value ? "1" : "0" }
        };
        this.request(payload).then(response => {
          this.updateUserSetting(response);
          this.darkModeLoading = false;
        });
      }
    },
    menuVisibility: {
      get() {
        return this.visibility;
      },
      set(value) {
        this.setVisibility(value);
      }
    },
    nameInitials() {
      const initials = _.split(this.user.name, " ", 2);
      if (initials.length === 2) {
        return `${initials[0].charAt(0)}${initials[1].charAt(0)}`;
      } else {
        return this.user.name.charAt(0);
      }
    }
  },

  methods: {
    ...mapMutations(["updateUserSetting"]),
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("menu", ["setVisibility"]),
    ...mapActions("requests", ["request"]),
    ...mapActions("config", ["setMasEnv"]),
    ...mapActions(["cashRegisterLogout"]),

    menuAction(item) {
      if (_.has(item, "action.method")) {
        console.log(item.action.method);
        switch (item.action.method) {
          case "changePasswordDialog":
            this.changePasswordDialog();
            break;
          default:
            break;
        }
      }
    },
    menuLink(item) {
      if (_.has(item, "action.link")) {
        return item.action.link;
      }
    },
    txtColor(mode) {
      switch (mode) {
        case "local":
        case "test":
        case "development":
          return "orange--text";
        case "live":
        case "production":
          return "green--text";
        default:
          return "white--text";
      }
    },
    closeCashRegisterDialog() {
      this.setDialog({
        show: true,
        width: 600,
        title: `Close and generate Z report`,
        titleCloseBtn: true,
        icon: "mdi-alpha-z-circle",
        component: "closeCashRegisterForm",
        persistent: true,
        eventChannel: "top-menu-generate-z"
      });
    },
    changePasswordDialog() {
      this.setDialog({
        show: true,
        width: 600,
        title: `Change Password`,
        titleCloseBtn: true,
        model: { action: "change_self" },
        icon: "mdi-key",
        component: "PasswordForm",
        persistent: true
      });
    },
    displayZDialog(report) {
      this.setDialog({
        show: true,
        width: 1000,
        title: report.report_name,
        titleCloseBtn: true,
        model: report,
        icon: "mdi-alpha-z-circle",
        component: "cashRegisterReports",
        persistent: true
      });
    },
    checkCashRegisterDialog() {
      this.setDialog({
        show: true,
        width: 1000,
        title: `Report: ${this.cashRegister.name}`,
        titleCloseBtn: true,
        icon: "mdi-alpha-x-circle",
        component: "cashRegisterReports",
        persistent: true
      });
    }
  }
};
</script>
