<template>
    <v-snackbar
        v-if="show"
        v-model="show"
        :color="type"
        :type="type"
        top
        class="d-flex align-center"
    >
        <v-icon large>{{ icon() }}</v-icon>
        <span class="pl-3" v-html="displayMessages()"></span>
        <v-spacer></v-spacer>
    </v-snackbar>
</template>

<script>
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
        show: {
            get() {
                return this.$store.state.notification.msg ? true : false;
            },
            set() {
                this.$store.state.notification.msg = "";
                this.$store.state.notification.type = undefined;
            }
        },
        message() {
            return this.$store.state.notification.msg;
        },
        type() {
            return this.$store.state.notification.type;
        }
    },
    methods: {
        icon() {
            return _.find(this.icons, { name: this.type }).icon;
        },
        displayMessages() {
            if (typeof this.message === "object") {
                let msg = "";

                for (const [key, value] of Object.entries(this.message)) {
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
                return _.capitalize(this.message);
            }
        }
    }
};
</script>
