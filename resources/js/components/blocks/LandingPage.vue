<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="4">
                <v-img
                    contain
                    src="https://www.plantshed.com//skin/frontend/plantshed/default/images/ps-logo.svg"
                ></v-img>
            </v-col>
            <v-col cols="12" align="center" justify="center">
                <v-progress-circular
                    :color="color"
                    :size="100"
                    :value="loadPercent"
                >{{ loadPercent }}</v-progress-circular>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
export default {
    mounted() {
        this.init();
    },
    beforeDestroy() {
        clearInterval(this.retrieveCashRegisterEvent);
    },
    data() {
        return {
            color: "primary",
            error_txt: ""
        };
    },
    computed: {
        ...mapState(["appLoad"]),

        loadPercent: {
            get() {
                if (this.appLoad > 100) {
                    return 100;
                } else {
                    return this.appLoad;
                }
            },
            set(value) {
                this.addLoadPercent(value);
            }
        }
    },
    methods: {
        ...mapMutations(["addLoadPercent"]),
        ...mapMutations("cart", ["resetState"]),
        ...mapMutations("dialog", ["resetDialog"]),
        ...mapActions(["getAppConfig", "retrieveCashRegister"]),

        init() {
            this.resetAppState();
            this.redirectToDashboard();

            this.getAppConfig()
                .then(() => {
                    this.loadPercent = 30;
                })
                .catch(error => {
                    this.setColor(true);
                });
            this.retrieveCashRegister()
                .then(() => {
                    this.loadPercent = 50;
                })
                .catch(error => {
                    this.setColor(true);
                });
        },
        redirectToDashboard() {
            if (this.$route.name !== "dashboard") {
                this.$router.push({ name: "dashboard" });
            }

            this.loadPercent = 10;
        },
        resetAppState() {
            this.resetState();
            this.resetDialog();
            this.loadPercent = 10;
        },
        setError(error) {
            if (error) {
                this.color = "danger";
                this.error = error;
            }
        }
    }
};
</script>
