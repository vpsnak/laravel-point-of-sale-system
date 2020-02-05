<template>
    <v-app id="app">
        <notification />

        <interactiveDialog
            v-if="auth && dialog.show"
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

        <sideMenu v-if="appLoad === 100 && auth" />

        <topMenu v-if="appLoad === 100 && auth" />

        <v-content>
            <transition name="fade">
                <router-view v-if="(auth && appLoad === 100 ) || !auth"></router-view>
                <landingPage v-else-if="appLoad !== 100" />
            </transition>
        </v-content>
    </v-app>
</template>

<script>
import { mapGetters, mapMutations, mapState } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
    computed: {
        ...mapState("dialog", ["interactive_dialog"]),
        ...mapState(["appLoad"]),

        loadPercent() {
            return this.appLoad;
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
                this.logout();
                return false;
            }
        },
        ...mapGetters(["authorized", "role"])
    },
    methods: {
        ...mapMutations(["logout"]),
        ...mapMutations("dialog", ["setDialog", "resetDialog"]),

        dialogEvent(event) {
            console.log(event);
            EventBus.$emit("dialog", event);
            this.resetDialog();
        }
    },
    beforeDestroy() {
        EventBus.$off();
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
