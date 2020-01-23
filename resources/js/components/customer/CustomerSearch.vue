<template>
    <div>
        <v-row align="center" justify="center">
            <v-combobox
                :no-filter="true"
                v-if="editable"
                v-model="cartCustomer"
                clearable
                dense
                :items="results"
                :loading="loading"
                :search-input.sync="search"
                color="white"
                hide-no-data
                hide-selected
                :item-text="getCustomerFullname"
                label="Select customer"
                placeholder="Start typing to Search"
                prepend-icon="mdi-account-search"
                return-object
                @blur="checkIfObjectEvent"
            ></v-combobox>

            <v-text-field
                v-else
                :value="getCustomerFullname(cartCustomer)"
                disabled
                prepend-icon="person"
            ></v-text-field>

            <v-tooltip bottom v-if="cartCustomer">
                <template v-slot:activator="{ on }">
                    <v-btn
                        class="mx-1"
                        @click.stop="customerForm(false)"
                        v-on="on"
                        icon
                        small
                        :disabled="!editable"
                    >
                        <v-icon small>mdi-pencil</v-icon>
                    </v-btn>
                </template>
                <span>View / Edit selected customer</span>
            </v-tooltip>

            <v-tooltip bottom v-if="cartCustomerComment">
                <template v-slot:activator="{ on }">
                    <v-btn
                        class="mx-1"
                        @click.stop="viewCustomerComments"
                        text
                        icon
                        color="red"
                        small
                    >
                        <v-icon small>mdi-comment</v-icon>
                    </v-btn>
                </template>
                <span>View comments</span>
            </v-tooltip>

            <v-divider inset vertical class="mx-2"></v-divider>

            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn
                        class="mx-1"
                        @click.stop="customerForm(true)"
                        v-on="on"
                        icon
                        :disabled="!editable"
                        small
                    >
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>
                <span>Add a customer</span>
            </v-tooltip>

            <interactiveDialog
                v-if="dialog.show"
                :show="dialog.show"
                :title="dialog.title"
                :titleCloseBtn="dialog.titleCloseBtn"
                :icon="dialog.icon"
                :fullscreen="dialog.fullscreen"
                :width="dialog.width"
                :component="dialog.component"
                :content="dialog.content"
                :model="dialog.model"
                @action="dialogEvent"
                :persistent="dialog.persistent"
            ></interactiveDialog>
        </v-row>
    </div>
</template>
<script>
export default {
    props: {
        keywordLength: Number,
        editable: Boolean | undefined
    },

    data() {
        return {
            dialog: {
                show: false,
                width: 600,
                fullscreen: false,
                icon: "",
                title: "",
                titleCloseBtn: false,
                component: "",
                content: "",
                model: "",
                persistent: false
            },
            loading: false,
            search: null,
            showCustomerComments: false,
            showCreateDialog: false,
            customers: []
        };
    },
    computed: {
        results: {
            get() {
                return this.customers;
            },
            set(value) {
                this.customers = value;
            }
        },
        cartCustomer: {
            get() {
                return this.$store.state.cart.customer;
            },
            set(value) {
                if (value !== this.cartCustomer) {
                    this.$store.commit("cart/resetShipping", value);
                }
                this.$store.commit("cart/setCustomer", value);
            }
        },
        cartCustomerComment() {
            if (this.cartCustomer) {
                return this.cartCustomer.comment;
            } else {
                return false;
            }
        }
    },
    methods: {
        dialogEvent(event) {
            if (event.customer) {
                this.cartCustomer = event.customer;
            }

            this.resetDialog();
        },
        resetDialog() {
            this.dialog = {
                show: false,
                width: 600,
                fullscreen: false,
                title: "",
                titleCloseBtn: false,
                icon: "",
                component: "",
                content: "",
                model: "",
                persistent: false
            };

            this.action = "";
        },
        customerForm(create) {
            this.dialog = {
                show: true,
                width: 600,
                fullscreen: false,
                icon: "fas fa-user",
                title: create
                    ? "Add new customer"
                    : `View / Edit customer #${this.cartCustomer.id}`,
                titleCloseBtn: true,
                component: create ? "customerNewForm" : "customerForm",
                content: "",
                model: create ? {} : this.cartCustomer,
                persistent: create ? true : false
            };
        },
        viewCustomerComments() {
            this.dialog = {
                show: true,
                width: 600,
                fullscreen: false,
                icon: "mdi-comment",
                title: `Comments for customer #${this.cartCustomer.id}`,
                titleCloseBtn: true,
                component: "customerComment",
                content: "",
                model: this.cartCustomer,
                persistent: false
            };
        },
        checkIfObjectEvent() {
            if (!_.isObjectLike(this.cartCustomer)) {
                this.search = null;
            }
        },
        getCustomerFullname(item) {
            if (item) {
                return item.first_name + " " + item.last_name;
            } else {
                return "Guest";
            }
        },
        searchCustomer(keyword) {
            this.loading = true;
            const payload = {
                model: "customers",
                keyword: keyword
            };

            this.$store
                .dispatch("search", payload)
                .then(result => {
                    this.results = result;
                })
                .finally(() => (this.loading = false));
        }
    },
    watch: {
        search(keyword) {
            if (keyword) {
                if (keyword.length >= this.$props.keywordLength) {
                    if (this.loading) {
                        return;
                    } else {
                        this.searchCustomer(keyword);
                        return;
                    }
                }
            }
        }
    }
};
</script>
