<template>
  <v-app-bar app clipped-left>
    <v-app-bar-nav-icon
      @click.stop="menuVisibility = !menuVisibility"
    ></v-app-bar-nav-icon>
    <v-toolbar-title>
      {{ app_name }}
    </v-toolbar-title>

    <v-spacer></v-spacer>

    <v-toolbar-title class="mr-2">
      Environment:
      <i :class="txtColor(app_env)">
        {{ app_env }}
      </i>
    </v-toolbar-title>

    <v-spacer></v-spacer>

    <v-toolbar-title class="mr-2">
      MAS Env:
      <i :class="txtColor(mas_env)" @click="clicks++">
        {{ mas_env }}
      </i>
    </v-toolbar-title>

    <v-spacer></v-spacer>

    <v-chip
      v-if="!cashRegister"
      text
      @click="$router.push({ name: 'openCashRegister' })"
    >
      Select cash register
    </v-chip>
    <v-menu
      v-if="cashRegister"
      left
      bottom
      offset-x
      transition="scale-transition"
    >
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon dark>
          <v-avatar color="green" size="36">
            <v-icon dark>mdi-cash-register</v-icon>
          </v-avatar>
        </v-btn>
      </template>
      <v-list dense>
        <v-list-item-group>
          <v-list-item inactive two-line @click.stop :ripple="false">
            <v-list-item-avatar color="green">
              <v-icon>mdi-cash-register</v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>
                {{ store_name }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ cashRegister.name }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-divider />
          <v-list-item @click="checkCashRegisterDialog">
            <v-list-item-avatar>
              <v-icon color="secondary">mdi-alpha-x-circle</v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>Generate X report</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item @click.stop="cashRegisterLogout()">
            <v-list-item-avatar>
              <v-icon color="secondary">mdi-logout</v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item @click.stop="closeCashRegisterDialog()">
            <v-list-item-avatar>
              <v-icon color="red">mdi-alpha-z-circle</v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>
                Close and generate Z report
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-menu>
    <v-menu left bottom offset-x transition="scale-transition">
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon>mdi-account-circle</v-icon>
        </v-btn>
      </template>
      <v-list dense>
        <v-list-item-group>
          <v-list-item inactive two-line @click.stop :ripple="false">
            <v-list-item-avatar color="orange">
              {{ nameInitials }}
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>
                {{ user.name }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ user.email }}
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
            <v-list-item-avatar>
              <v-icon>{{ menu_item.icon }}</v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>{{ menu_item.title }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-divider />

          <v-list-item @click.stop="darkMode = !darkMode">
            <v-list-item-avatar>
              <v-icon>mdi-brightness-4</v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>Dark mode</v-list-item-title>
            </v-list-item-content>
            <v-list-item-action>
              <v-switch
                v-model="darkMode"
                @click.stop="darkMode = !darkMode"
              ></v-switch>
            </v-list-item-action>
          </v-list-item>
        </v-list-item-group>
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
      clicks: 0
    };
  },

  watch: {
    clicks(value) {
      if (value === 10) {
        this.clicks = 0;
        let payload;

        if (this.mas_env === "production") {
          payload = "test";
        } else {
          payload = "production";
        }

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
    ...mapMutations("dialog", ["setDialog"]),
    ...mapMutations("menu", ["setVisibility"]),
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
