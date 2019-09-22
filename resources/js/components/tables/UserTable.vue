<template>
    <v-card>
        <v-card-title>
            Users
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
        </v-card-title>
        <v-data-table :headers="headers" :items="userList" :search="search" :loading="loader">
            <template v-slot:item.email="{item}">
                <a :href="'mailto:' + item.email">{{ item.email }}</a>
            </template>
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
            model: "users",
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
                    text: "E-mail",
                    value: "email",
                    sortable: false
                },
                {
                    text: "E-mail verified",
                    value: "email_verified_at",
                    sortable: false
                },
                {
                    text: "Password",
                    value: "password",
                    sortable: false
                }
            ]
        };
    },
    mounted() {
        this.getAllUsers();
    },
    computed: {
        userList: {
            get() {
                return this.$store.state.userList;
            },
            set(value) {
                this.$store.state.userList = value;
            }
        }
    },
    methods: {
        applyFilter(filter) {},
        initiateLoadingSearchResults(loading) {
            if (loading) {
                this.loader = true;
                this.userList = [];
            } else {
                this.loader = false;
            }
        },
        getAllUsers() {
            this.initiateLoadingSearchResults(true);

            let payload = {
                model: "users",
                mutation: "setUserList"
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