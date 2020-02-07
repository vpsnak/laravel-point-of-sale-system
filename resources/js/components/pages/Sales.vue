<template>
    <v-container fluid>
        <v-row dense>
            <v-col :lg="7">
                <productList />
            </v-col>
            <v-col :lg="5">
                <cart :editable="true" :actions="true" :toggles="true" />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            visibility: false
        };
    }
};
</script>
