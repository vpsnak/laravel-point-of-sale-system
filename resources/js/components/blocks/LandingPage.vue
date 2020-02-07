<template>
    <v-container v-if="this.app_load <= 100" class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="4">
                <v-img
                    contain
                    src="https://www.plantshed.com//skin/frontend/plantshed/default/images/ps-logo.svg"
                ></v-img>
            </v-col>
            <v-col cols="12" align="center" justify="center">
                <v-progress-circular
                    rotate="270"
                    :color="color"
                    :size="150"
                    :value="error_txt ? 100 : loadPercent"
                    :width="18"
                >
                    <b v-if="!error_txt">{{ loadPercent }} %</b>
                    <b v-else>{{ error_txt }}</b>
                </v-progress-circular>
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
            color: "white",
            error_txt: ""
        };
    },

    computed: {
        ...mapState(["token"]),
        ...mapState("config", ["app_load"]),

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
            }
        }
    },
    methods: {
        ...mapMutations(["logout"]),
        ...mapMutations("config", ["addLoadPercent", "resetLoad"]),
        ...mapMutations("cart", ["resetState"]),
        ...mapMutations("dialog", ["resetDialog"]),
        ...mapActions(["retrieveCashRegister"]),
        ...mapActions("config", ["getMasEnv"]),

        init() {
            this.resetLoad();
            this.resetAppState();

            this.getMasEnv().then(() => {
                this.loadPercent = 45;
            });
            this.retrieveCashRegister()
                .then(() => {
                    this.loadPercent = 45;
                })
                .catch(error => {
                    this.loadPercent = 0;
                    this.color = "red";
                    this.error_txt = "Error";

                    if (error.response.status === 401) {
                        this.error_txt = "Unauthorized";
                        this.logout();
                    }
                });
        },
        redirect() {
            if (this.$route.name !== "dashboard") {
                this.$router.push({ name: "dashboard" });
            }
        },
        resetAppState() {
            this.resetState();
            this.resetDialog();
            this.loadPercent = 10;
        }
    }
};
</script>
