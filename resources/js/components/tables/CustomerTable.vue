<template>
    <v-card>
        <v-card-title>
            Customers
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
        </v-card-title>
        <v-data-table :headers="headers" :items="customerList" :search="search" :loading="loader">
            <template v-slot:item.email="{item}">
                <a :href="'mailto:' + item.email">{{ item.email }}</a>
            </template>
            <template v-slot:item.phone="{item}">
                <a :href="'tel:' + item.phone">{{ item.phone }}</a>
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
            model: "customers",
            search: "",
            headers: [
                {
                    text: "Id",
                    value: "id"
                },
                {
                    text: "First name",
                    value: "first_name"
                },
                {
                    text: "Last name",
                    value: "last_name"
                },
                {
                    text: "E-mail",
                    value: "email",
                    sortable: false
                },
                {
                    text: "Phone",
                    value: "phone",
                    sortable: false
                },
                {
                    text: "Company name",
                    value: "company_name",
                    sortable: false
                },
                {
                    text: "Address",
                    value: "addresses[0].street",
                    sortable: false
                }
            ]
        };
    },
    mounted() {
        this.getAllCustomers();
    },
    computed: {
        customerList: {
            get() {
                return this.$store.state.customerList;
            },
            set(value) {
                this.$store.state.customerList = value;
            }
        }
    },
    methods: {
        applyFilter(filter) {},
        initiateLoadingSearchResults(loading) {
            if (loading) {
                this.loader = true;
                this.customerList = [];
            } else {
                this.loader = false;
            }
        },
        getAllCustomers() {
            this.initiateLoadingSearchResults(true);

            let payload = {
                model: "customers",
                mutation: "setCustomerList"
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