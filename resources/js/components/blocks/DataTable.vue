<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                <v-icon v-if="$props.icon" class="mr-2">{{
                    $props.icon
                }}</v-icon>
                {{ $props.title }}
                <v-spacer></v-spacer>
                <v-text-field
                    :disabled="loading"
                    :search="search"
                    prepend-icon="search"
                    hide-details
                    label="Search"
                    single-line
                    v-model="keyword"
                    append-icon="mdi-close"
                    @click:append="
                        (keyword = null),
                            (searchAction = null),
                            paginate($event)
                    "
                    @click:prepend="search"
                    @keyup.enter="search"
                ></v-text-field>
                <v-divider
                    class="mx-4"
                    v-if="$props.newForm && $props.btnTxt"
                    inset
                    vertical
                ></v-divider>
                <v-btn
                    v-if="$props.newForm && $props.btnTxt"
                    :disabled="$props.disableNewBtn || loading"
                    color="primary"
                    @click="createItemDialog"
                    >{{ $props.btnTxt }}</v-btn
                >
            </v-card-title>

            <v-data-table
                disable-sort
                dense
                :disable-filtering="true"
                :headers="$props.headers"
                :items="rows"
                :loading="loading"
                :items-per-page="15"
                :page.sync="currentPage"
                :server-items-length="totalItems"
                @pagination="paginate"
                :footer-props="footerProps"
            >
                <template
                    v-for="(_, slot) of $scopedSlots"
                    v-slot:[slot]="scope"
                >
                    <slot :name="slot" v-bind="scope" />
                </template>

                <v-alert
                    :value="true"
                    color="error"
                    icon="warning"
                    slot="no-results"
                    >Your search for "{{ keyword }}" found no results.</v-alert
                >
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";
import { EventBus } from "../../plugins/event-bus";

export default {
    data() {
        return {
            current_page: 1,
            total_items: null,
            action: "",
            keyword: "",
            search_action: false
        };
    },
    props: {
        icon: String,
        title: String,
        headers: Array,
        dataUrl: String,
        btnTxt: String,
        newForm: String,
        disableNewBtn: Boolean
    },
    mounted() {
        EventBus.$on("data-table", event => {
            console.log(event);

            switch (event.action) {
                case "paginate":
                    this.paginate();
                    break;
                case "search":
                    this.keyword = event.keyword || null;
                    this.search();
                default:
                    if (_.isBoolean(event.payload) && event.payload) {
                        this.paginate();
                    }
                    break;
            }
        });

        if (this.$props.tableForm === "productForm") {
            this.$root.$on("barcodeScan", sku => {
                this.keyword = sku;
                this.search();
            });
        }
    },
    beforeDestroy() {
        EventBus.$off();
        if (this.$props.tableForm === "productForm") {
            this.$root.$off("barcodeScan");
        }
    },
    computed: {
        searchAction: {
            get() {
                return this.search_action;
            },
            set(value) {
                this.search_action = value;
            }
        },
        footerProps() {
            return {
                "disable-pagination": this.loading,
                "disable-items-per-page": true,
                options: { itemsPerPage: 15, page: this.currentPage }
            };
        },
        totalItems: {
            get() {
                return this.total_items;
            },
            set(value) {
                this.total_items = value;
            }
        },
        currentPage: {
            get() {
                return this.current_page;
            },
            set(value) {
                this.current_page = value;
            }
        },
        ...mapState("datatable", {
            rows: "rows",
            loading: "loading"
        })
    },
    methods: {
        ...mapMutations("dialog", ["setDialog"]),

        search(e, page = false) {
            if (this.keyword.length > 2 || this.searchAction) {
                this.setLoading(true);

                if (!page) {
                    this.searchAction = this.keyword;
                } else {
                    if (!this.keyword) {
                        this.keyword = this.searchAction;
                    }
                }

                let payload = {
                    model: this.dataUrl,
                    page: page || 1,
                    keyword: this.keyword,
                    dataTable: true
                };

                this.searchRequest(payload)
                    .then(response => {
                        this.setRows(response.data);

                        if (response.total !== this.totalItems) {
                            this.totalItems = response.total;
                        }
                    })
                    .finally(() => {
                        this.setLoading(false);
                    });
            }
        },

        paginate(event) {
            this.setRows([]);

            if (this.searchAction) {
                this.search(null, event.page);
            } else {
                this.searchAction = false;

                this.setLoading(true);

                this.getAll({
                    model: this.dataUrl,
                    page: event ? event.page : this.currentPage,
                    dataTable: true
                })
                    .then(response => {
                        this.setRows(response.data);

                        if (response.total !== this.totalItems) {
                            this.totalItems = response.total;
                        }
                    })
                    .catch(() => {})
                    .finally(() => {
                        this.setLoading(false);
                    });
            }
        },

        createItemDialog() {
            this.setDialog = {
                show: true,
                width: 600,
                title: this.$props.btnTxt,
                titleCloseBtn: true,
                component: this.$props.newForm,
                persistent: true
            };
        },

        ...mapMutations("datatable", {
            setRows: "setRows",
            setLoading: "setLoading"
        }),

        ...mapActions({
            getAll: "getAll",
            searchRequest: "search"
        })
    }
};
</script>
