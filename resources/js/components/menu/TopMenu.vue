<template>
    <v-app-bar app clipped-left>
        <v-app-bar-nav-icon @click.stop="toggle = !toggle"></v-app-bar-nav-icon>
        <v-toolbar-title>{{ app_name }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-toolbar-title class="pr-2">
            Environment:
            <i :class="txtColor(app_env)">{{ app_env }}</i>
        </v-toolbar-title>

        <v-spacer></v-spacer>

        <v-toolbar-title class="pr-2">
            MAS Env:
            <i :class="txtColor(mas_env)" @click="clicks++">{{ mas_env }}</i>
        </v-toolbar-title>

        <v-spacer></v-spacer>

        <v-chip
            v-if="!openedRegister"
            text
            @click="$router.push({ name: 'openCashRegister' })"
            >Select cash register</v-chip
        >
        <v-menu
            v-if="openedRegister"
            left
            bottom
            offset-x
            transition="scale-transition"
        >
            <template v-slot:activator="{ on }">
                <v-btn v-on="on" icon>
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
                            <v-list-item-title>{{
                                store.name
                            }}</v-list-item-title>
                            <v-list-item-subtitle>{{
                                cashRegister.name
                            }}</v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider />
                    <v-list-item @click="checkCashRegisterDialog">
                        <v-list-item-avatar>
                            <v-icon color="secondary"
                                >mdi-alpha-x-circle</v-icon
                            >
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title
                                >Generate X report</v-list-item-title
                            >
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item @click="cashRegisterLogout()">
                        <v-list-item-avatar>
                            <v-icon color="secondary">mdi-logout</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item @click="closeCashRegisterDialog()">
                        <v-list-item-avatar>
                            <v-icon color="red">mdi-alpha-z-circle</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title
                                >Close and generate Z report</v-list-item-title
                            >
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
                        <v-list-item-avatar color="orange">{{
                            user.name.charAt(0)
                        }}</v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>{{
                                user.name
                            }}</v-list-item-title>
                            <v-list-item-subtitle>{{
                                user.email
                            }}</v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider />
                    <v-list-item to="/logout">
                        <v-list-item-avatar>
                            <v-icon>mdi-logout-variant</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>Sign out</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item @click="changePasswordDialog">
                        <v-list-item-avatar>
                            <v-icon>mdi-key</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title
                                >Change password</v-list-item-title
                            >
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
import { EventBus } from "../../plugins/event-bus";

export default {
    mounted() {
        EventBus.$on("top-menu-");
    },
    beforeDestroy() {
        EventBus.$off();
    },

    data() {
        return {
            clicks: 0
        };
    },
    watch: {
        clicks(value) {
            if (value === 10) {
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
    methods: {
        ...mapMutations("dialog", ["setDialog"]),
        ...mapActions("config", ["setMasEnv"]),

        invertTheme() {
            this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
        },
        cashRegisterLogout() {
            this.$store.dispatch("logoutCashRegister");
        },

        dialogEvent(event) {
            this.displayZDialog(event.response.report);
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
                width: 600,
                title: `Close and generate Z report`,
                titleCloseBtn: true,
                icon: "mdi-alpha-z-circle",
                component: "closeCashRegisterForm",
                persistent: true
            });
        },
        changePasswordDialog() {
            this.setDialog({
                width: 600,
                title: `Change Password`,
                titleCloseBtn: true,
                model: { action: "change_self" },
                icon: "mdi-key",
                component: "PasswordForm",
                persistent: true
            });
        },
        displayZDialog(event) {
            this.setDialog({
                width: 1000,
                title: event.report_name,
                titleCloseBtn: true,
                model: event,
                icon: "mdi-alpha-z-circle",
                component: "cashRegisterReports",
                persistent: true
            });
        },
        checkCashRegisterDialog() {
            this.setDialog({
                width: 1000,
                title: `Report: ${this.cashRegister.name}`,
                titleCloseBtn: true,
                icon: "mdi-alpha-x-circle",
                component: "cashRegisterReports",
                persistent: true
            });
        }
    },

    computed: {
        ...mapState("config", ["app_env", "app_name", "mas_env"]),
        ...mapState(["user", "cashRegister", "store"]),

        openedRegister() {
            return this.$store.getters.openedRegister;
        },
        darkMode: {
            get() {
                return this.$vuetify.theme.dark;
            },
            set(value) {
                this.$vuetify.theme.dark = value;
            }
        },
        toggle: {
            get() {
                return this.$store.state.menu.toggle;
            },
            set(value) {
                this.$store.commit("menu/toggleMenu", value);
            }
        }
    }
};
</script>
