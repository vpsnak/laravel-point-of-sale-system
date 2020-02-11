<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                <v-icon v-if="data_table.icon" class="mr-2">
                    {{ data_table.icon }}
                </v-icon>
                {{ data_table.title }}
                <v-spacer></v-spacer>
                <v-text-field
                    :disabled="data_table.loading"
                    :search="search"
                    prepend-icon="search"
                    hide-details
                    label="Search"
                    single-line
                    v-model="keyword"
                    append-icon="mdi-close"
                    @click:append="
                        (keyword = null),
                            (search_action = null),
                            paginate($event)
                    "
                    @click:prepend="search"
                    @keyup.enter="search"
                ></v-text-field>
                <v-divider
                    class="mx-4"
                    v-if="data_table.newForm && data_table.btnTxt"
                    inset
                    vertical
                ></v-divider>
                <v-btn
                    v-if="data_table.newForm && data_table.btnTxt"
                    :disabled="data_table.disableNewBtn || data_table.loading"
                    color="primary"
                    @click="createItemDialog()"
                >
                    {{ data_table.btnTxt }}
                </v-btn>
            </v-card-title>

            <v-data-table
                disable-sort
                dense
                :disable-filtering="true"
                :headers="getHeaders"
                :items="data_table.rows"
                :loading="data_table.loading"
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
                >
                    Your search for "{{ keyword }}" found no results.
                </v-alert>
            </v-data-table>
        </v-card>
    </v-container>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
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

    mounted() {
        console.log(this.getHeaders);
        EventBus.$on("data-table", event => {
            console.info({ component: "data-table", event });
            if (_.has(event, "payload.action")) {
                switch (event.payload.action) {
                    case "paginate":
                        this.paginate();
                        break;
                    case "search":
                        this.keyword = event.keyword || null;
                        this.search();
                        break;
                    default:
                        if (_.isBoolean(event.payload) && event.payload) {
                            this.paginate();
                        }
                        break;
                }
            } else if (event.payload) {
                console.warn([
                    "Unknown event",
                    { component: "data-table", event }
                ]);
            }
        });

        if (this.data_table.newForm === "productForm") {
            this.$root.$on("barcodeScan", sku => {
                this.keyword = sku;
                this.search();
            });
        }
    },
    beforeDestroy() {
        this.resetDataTable();
        EventBus.$off();
        if (this.data_table.newForm === "productForm") {
            this.$root.$off("barcodeScan");
        }
    },
    computed: {
        ...mapGetters("datatable", ["getHeaders"]),
        ...mapState("datatable", ["data_table"]),

        footerProps() {
            return {
                "disable-pagination": this.data_table.loading,
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
        }
    },
    methods: {
        ...mapMutations("dialog", ["setDialog"]),
        ...mapMutations("datatable", [
            "setRows",
            "setLoading",
            "resetDataTable"
        ]),
        ...mapActions(["getAll", "search"]),

        search(e, page = false) {
            if (this.keyword.length > 2 || this.search_action) {
                this.setLoading(true);

                if (!page) {
                    this.search_action = this.keyword;
                } else {
                    if (!this.keyword) {
                        this.keyword = this.search_action;
                    }
                }

                let payload = {
                    model: this.data_table.model,
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

            if (this.search_action) {
                this.search(null, event.page);
            } else {
                this.search_action = false;

                this.setLoading(true);
                this.getAll({
                    model: this.data_table.model,
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
            this.setDialog({
                show: true,
                width: 600,
                title: this.data_table.btnTxt,
                titleCloseBtn: true,
                component: this.data_table.newForm,
                persistent: true
            });
        }
    }
};
</script>
