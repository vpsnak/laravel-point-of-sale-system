<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="stores"
        tableTitle="Stores"
        tableBtnTitle="New Store"
        tableForm="storeForm"
        tableViewComponent="store"
    ></prop-data-table>
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
                    text: "Phone",
                    value: "phone"
                },
                {
                    text: "Street",
                    value: "street"
                },
                {
                    text: "Postcode",
                    value: "postcode"
                },
                {
                    text: "City",
                    value: "city"
                },
                {
                    text: "Tax Id",
                    value: "tax_id"
                },
                {
                    text: "Created by",
                    value: "created_by"
                },
                {
                    text: "Actions",
                    value: "action"
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
