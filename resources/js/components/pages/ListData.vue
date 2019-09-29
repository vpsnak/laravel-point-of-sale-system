<template>
    <v-card color="basil">
        <v-tabs v-model="tab" background-color="transparent" color="basil" grow>
            <v-tab key="stores">Stores</v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item key="stores">
                <v-card flat color="basil">
                    <v-card-text>
                        <store-table />
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
    </v-card>
</template>
<script>
export default {
    data() {
        return {
            tab: null
        };
    }
};
</script>
