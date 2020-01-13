<template>
    <v-app-bar app clipped-left>
        <v-app-bar-nav-icon @click.stop="toggle = !toggle"></v-app-bar-nav-icon>

        <v-toolbar-title>Plantshed</v-toolbar-title>

        <div class="flex-grow-1"></div>

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
                    <v-list-item @click="closeCashRegisterDialog">
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

        <interactiveDialog
            v-if="dialog.show"
            :show="dialog.show"
            :title="dialog.title"
            :titleCloseBtn="dialog.titleCloseBtn"
            :icon="dialog.icon"
            :fullscreen="dialog.fullscreen"
            :width="dialog.width"
            :component="dialog.component"
            :content="dialog.content"
            :model="dialog.model"
            @action="dialogEvent"
            :persistent="dialog.persistent"
        ></interactiveDialog>
    </v-app-bar>
</template>

<script>
import { mapState } from "vuex";

export default {
    data() {
        return {
            dialog: {
                show: false,
                width: 600,
                fullscreen: false,
                icon: "",
                title: "",
                titleCloseBtn: false,
                component: "",
                content: "",
                model: "",
                persistent: false
            },
            action: ""
        };
    },
    methods: {
        invertTheme() {
            this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
        },
        cashRegisterLogout() {
            this.$store.dispatch("logoutCashRegister");
        },
        resetDialog() {
            this.dialog = {
                show: false,
                width: 600,
                fullscreen: false,
                title: "",
                titleCloseBtn: false,
                icon: "",
                component: "",
                content: "",
                model: "",
                persistent: false
            };

            this.action = "";
        },
        dialogEvent(event) {
            if (event) {
                switch (this.action) {
                    case "closeCashRegister":
                        break;
                    default:
                        break;
                }
            }

            this.resetDialog();
        },
        closeCashRegisterDialog() {
            this.dialog = {
                show: true,
                width: 600,
                title: `Close and generate Z report`,
                titleCloseBtn: true,
                icon: "mdi-alpha-z-circle",
                component: "closeCashRegisterForm",
                persistent: true
            };

            this.action = "closeCashRegister";
        },
        changePasswordDialog() {
            this.dialog = {
                show: true,
                width: 600,
                title: `Change Password`,
                titleCloseBtn: true,
                model: { action: "change_self" },
                icon: "mdi-key",
                component: "PasswordForm",
                persistent: true
            };

            this.action = "result";
        },
        checkCashRegisterDialog() {
            this.dialog = {
                show: true,
                width: 1000,
                title: "Report: " + this.cashRegister.name,
                titleCloseBtn: true,
                icon: "mdi-alpha-x-circle",
                component: "cashRegisterReports",
                persistent: true
            };
            this.action = "result";
        }
    },

    computed: {
        openedRegister() {
            return this.$store.getters.openedRegister;
        },
        user() {
            return this.$store.state.user;
        },
        cashRegister() {
            return this.$store.state.cashRegister;
        },
        store() {
            return this.$store.state.store;
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
