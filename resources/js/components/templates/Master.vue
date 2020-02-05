<template>
    <v-app id="app">
        <notification />

        <interactiveDialog v-if="auth"></interactiveDialog>

        <sideMenu v-if="app_load > 100 && auth" />

        <topMenu v-if="app_load > 100 && auth" />

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
        if (this.auth && this.loadPercent <= 100) {
            this.$router.push({ name: "landingPage" });
        }
    },
    computed: {
        ...mapState("dialog", ["interactive_dialog"]),
        ...mapState("config", ["app_load"]),

        loadPercent() {
            return this.app_load;
        },
        dialog: {
            get() {
                return this.interactive_dialog;
            },
            set(value) {
                this.setDialog(value);
            }
        },
        auth() {
            if (this.authorized && this.role) {
                window.axios.defaults.headers.common[
                    "Authorization"
                ] = this.$store.state.token;
                return true;
            } else {
                return false;
            }
        },
        ...mapGetters(["authorized", "role"])
    },
    methods: {
        ...mapMutations(["logout"]),
        ...mapMutations("dialog", ["setDialog"])
    }
};
</script>

<style>
.fas,
.fab,
.fa {
    font-size: 1.2em !important;
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
