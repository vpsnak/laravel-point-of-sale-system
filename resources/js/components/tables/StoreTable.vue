<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="stores"
        tableTitle="Stores"
        tableBtnTitle="New Store"
        tableForm="storeForm"
        tableViewComponent="store"
    />
</template>

<script>
import { mapMutations } from "vuex";

export default {
    data() {
        return {
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
                    text: "Tax Id",
                    value: "tax_id"
                },
                {
                    text: "Taxable",
                    value: "taxable"
                },
                {
                    text: "Created by",
                    value: "created_by"
                },
                {
                    text: "Actions",
                    value: "action",
                    sortable: false
                }
            ]
        };
    },
    mounted() {
        this.setRows([]);
    },
    methods: {
        ...mapMutations("datatable", {
            setRows: "setRows",
            deleteRow: "deleteRow"
        })
    }
};
</script>
