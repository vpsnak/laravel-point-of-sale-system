<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="users"
        tableTitle="Users"
        tableBtnTitle="New User"
        tableForm="userForm"
        tableBtnDisable="true"
    />
</template>

<script>
  import { mapMutations } from 'vuex'

  export default {
    data() {
        return {
            headers: [
                {
                    text: "WebO2",
                    align: "left",
                    sortable: false,
                    value: "name"
                },
                { text: "Calories", value: "calories" },
                { text: "Fat (g)", value: "fat" },
                { text: "Carbs (g)", value: "carbs" },
                { text: "Protein (g)", value: "protein" },
                { text: "Actions", value: "action", sortable: false }
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
