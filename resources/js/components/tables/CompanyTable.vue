<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="companies"
        tableTitle="Companies"
        tableBtnTitle="New company"
        tableForm="companyForm"
        tableViewComponent="company"
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
