<template>
    <v-navigation-drawer clipped app v-model="menuVisibility">
        <v-list dense>
            <v-list-item
                v-for="menuItem in items"
                :key="menuItem.id"
                :to="menuItem.to"
            >
                <v-list-item-icon>
                    <v-icon>{{ menuItem.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{ menuItem.title }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-divider dark class="my-4"></v-divider>
            <v-subheader class="mt-5 grey--text text--darken-1"
                >"Designed" and "developed" by WebO2</v-subheader
            >
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
    computed: {
        ...mapState("menu", ["visibility"]),
        ...mapState("menu", ["adminItems"]),
        ...mapState("menu", ["managerItems"]),
        ...mapState("menu", ["cashierItems"]),

        menuVisibility: {
            get() {
                return this.visibility;
            },
            set(value) {
                this.setVisibility(value);
            }
        },
        role() {
            return this.$store.getters.role;
        },
        items() {
            switch (this.role) {
                case "admin":
                    return this.adminItems;
                case "store_manager":
                    return this.managerItems;
                case "cashier":
                    return this.cashierItems;
            }
        }
    },
    methods: {
        ...mapMutations("menu", ["setVisibility"])
    }
};
</script>
