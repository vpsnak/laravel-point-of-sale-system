<template>
    <div>
        <v-container>
            <v-row>
                <v-col :lg="8" class="py-lg-0 px-lg-1">
                    <productList />
                </v-col>
                <v-col :lg="4" class="pa-lg-0">
                    <cart
                        icon="shopping_cart"
                        title="Shopping cart"
                        :editable="true"
                        :actions="true"
                        :toggles="true"
                    />
                </v-col>
            </v-row>
        </v-container>
        <interactiveDialog
            v-if="visibility"
            :show="visibility"
            title="Select store and cash register"
            :width="600"
            component="storeRegForm"
            :titleCloseBtn="true"
        ></interactiveDialog>
    </div>
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
