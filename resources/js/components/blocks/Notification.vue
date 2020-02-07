<template>
    <v-snackbar
        v-if="notification.msg"
        v-model="notification.msg"
        :color="notification.type"
        :type="notification.type"
        top
        class="d-flex align-center"
    >
        <v-icon large>{{ icon() }}</v-icon>
        <span class="pl-3" v-html="displayMessages()"></span>
        <v-spacer></v-spacer>
    </v-snackbar>
</template>

<script>
import { mapState } from "vuex";

export default {
    data() {
        return {
            icons: [
                {
                    name: "success",
                    icon: "check_circle"
                },
                {
                    name: "info",
                    icon: "info"
                },
                {
                    name: "warning",
                    icon: "warning"
                },
                {
                    name: "error",
                    icon: "error"
                }
            ]
        };
    },

    computed: {
        ...mapState(["notification"])
    },

    methods: {
        icon() {
            return _.find(this.icons, { name: this.notification.type }).icon;
        },
        displayMessages() {
            if (_.isObjectLike(this.notification.msg)) {
                let msg = "";

                for (const [key, value] of Object.entries(
                    this.notification.msg
                )) {
                    if (this.type === "error") {
                        msg +=
                            "<strong>" +
                            _.capitalize(key) +
                            "</strong>" +
                            ": " +
                            value +
                            "<br>";
                    } else {
                        msg += value + "<br>";
                    }
                }

                return msg;
            } else {
                return _.capitalize(this.notification.msg);
            }
        }
    }
};
</script>
