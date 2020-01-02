<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="locations"
        tableTitle="Locations"
        tableBtnTitle="New Location"
        tableForm="locationForm"
        tableViewComponent="location"
    >
    </prop-data-table>
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
                    text: "Created at",
                    value: "created_at"
                },
                {
                    text: "Updated at",
                    value: "updated_at"
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
            setRows: "setRows"
        })
    }
};
</script>
