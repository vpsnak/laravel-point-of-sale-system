<template>
    <v-app id="app">
        <notification />

        <interactiveDialog v-if="showComponents"></interactiveDialog>

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
        if (this.auth && this.loadPercent <= 100) {
            this.$router.push({ name: "landingPage" });
        }
    },
    computed: {
        ...mapGetters(["auth", "role"]),
        ...mapState("dialog", ["interactive_dialog"]),
        ...mapState("config", ["app_load"]),

        showComponents() {
            if (this.app_load > 100 && this.auth) {
                return true;
            } else {
                return false;
            }
        },
        loadPercent() {
            return this.app_load;
        }
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
