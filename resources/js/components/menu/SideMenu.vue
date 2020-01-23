<template>
    <v-navigation-drawer clipped app dark v-model="toggle">
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
                >Designed and developed by WebO2</v-subheader
            >
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import { mapState } from "vuex";

export default {
    computed: {
        toggle: {
            get() {
                return this.$store.state.menu.toggle;
            },
            set(value) {
                this.$store.commit("menu/toggleMenu", value);
            }
        },
        role() {
            return this.$store.getters.role;
        },
        items() {
            switch (this.role) {
                case "admin":
                    return this.$store.state.menu.adminItems;
                case "store_manager":
                    return this.$store.state.menu.managerItems;
                case "cashier":
                    return this.$store.state.menu.cashierItems;
            }
        }
    }
};
</script>
