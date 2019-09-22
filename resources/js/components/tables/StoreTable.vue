<template>
    <v-card>
        <v-card-title>
            Stores
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
        </v-card-title>
        <v-data-table :headers="headers" :items="storeList" :search="search" :loading="loader">
            <v-alert
                slot="no-results"
                :value="true"
                color="error"
                icon="warning"
            >Your search for "{{ search }}" found no results.</v-alert>
        </v-data-table>
    </v-card>
</template>


<script>
export default {
    data() {
        return {
            loader: false,
            disableFilters: false,
            model: "stores",
            search: "",
            headers: [
                {
                    text: "Id",
                    value: "id"
                },
                {
                    text: "Name",
                    value: "name"
                },
                {
                    text: "Address Id",
                    value: "address_id"
                }
            ]
        };
    },
    mounted() {
        this.getAllStores();
    },
    computed: {
        storeList: {
            get() {
                return this.$store.state.storeList;
            },
            set(value) {
                this.$store.state.storeList = value;
            }
        }
    },
    methods: {
        applyFilter(filter) {},
        initiateLoadingSearchResults(loading) {
            if (loading) {
                this.loader = true;
                this.storeList = [];
            } else {
                this.loader = false;
            }
        },
        getAllStores() {
            this.initiateLoadingSearchResults(true);

            let payload = {
                model: "stores",
                mutation: "setStoreList"
            };
            this.$store
                .dispatch("getAll", payload)
                .then(result => {
                    this.initiateLoadingSearchResults(false);
                })
                .catch(error => {
                    this.initiateLoadingSearchResults(false);
                    console.log(error);
                });
        }
    }
};
</script>