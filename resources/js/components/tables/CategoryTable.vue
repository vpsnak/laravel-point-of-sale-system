<template>
    <v-card>
        <v-card-title>
            Categories
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
        </v-card-title>
        <v-data-table :headers="headers" :items="categoryList" :search="search" :loading="loader">
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
            model: "categories",
            search: "",
            headers: [
                {
                    text: "Id",
                    value: "id"
                },
                {
                    text: "Name",
                    value: "name"
                }
            ]
        };
    },
    mounted() {
        this.getAllCategories();
    },
    computed: {
        categoryList: {
            get() {
                return this.$store.state.categoryList;
            },
            set(value) {
                this.$store.state.categoryList = value;
            }
        }
    },
    methods: {
        initiateLoadingSearchResults(loading) {
            if (loading) {
                this.loader = true;
                this.disableFilters = true;
                this.categoryList = [];
            } else {
                this.loader = false;
                this.disableFilters = false;
            }
        },
        getAllCategories() {
            this.initiateLoadingSearchResults(true);

            let payload = {
                model: "categories",
                mutation: "setCategoryList"
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
        },
        searchCategory() {
            if (this.keyword.length > 0) {
                this.initiateLoadingSearchResults(true);

                let payload = {
                    model: "categories",
                    mutation: "setCategoryList",
                    keyword: this.keyword
                };

                this.$store
                    .dispatch("search", payload)
                    .then(result => {
                        this.initiateLoadingSearchResults(false);
                    })
                    .catch(error => {
                        this.initiateLoadingSearchResults(false);
                    });
            }
        }
    }
};
</script>