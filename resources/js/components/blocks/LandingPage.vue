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
        <v-row v-if="verbose && init_info.length">
            <v-col cols="12">
                <transition-group
                    name="staggered-fade"
                    tag="table"
                    v-bind:css="false"
                    @before-enter="beforeEnter"
                    @enter="enter"
                >
                    <tr
                        v-for="(item, index) in init_info"
                        :key="item.action"
                        :data-index="index"
                    >
                        <td>{{ item.action }}</td>
                        <td :class="item.status + '--text'">
                            {{ item.message }}
                        </td>
                    </tr>
                </transition-group>
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
        ...mapState("config", ["app_load", "verbose", "init_info"]),

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
        ...mapActions("config", ["retrieveCashRegister"]),
        ...mapActions("config", ["getMasEnv", "initPrivateChannels"]),

        init() {
            this.resetLoad();
            this.resetAppState();

            this.initPrivateChannels()
                .then(() => {
                    this.loadPercent = 30;
                })
                .catch(error => {
                    this.setError(error);
                });

            this.getMasEnv()
                .then(() => {
                    this.loadPercent = 30;
                })
                .catch(error => {
                    this.setError(error);
                });
            this.retrieveCashRegister()
                .then(() => {
                    this.loadPercent = 30;
                })
                .catch(error => {
                    this.setError(error);
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
        },
        setError(error) {
            this.loadPercent = 0;
            this.color = "red";
            this.error_txt = "Error";
            console.log(error);
        },

        // animations
        beforeEnter: function(el) {
            el.style.opacity = 0;
            el.style.height = 0;
        },
        enter: function(el, done) {
            var delay = el.dataset.index * 150;
            setTimeout(function() {
                Velocity(
                    el,
                    { opacity: 1, height: "1.6em" },
                    { complete: done }
                );
            }, delay);
        },
        leave: function(el, done) {
            var delay = el.dataset.index * 150;
            setTimeout(function() {
                Velocity(el, { opacity: 0, height: 0 }, { complete: done });
            }, delay);
        }
    }
};
</script>
