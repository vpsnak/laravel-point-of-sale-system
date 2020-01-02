<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="users"
        tableTitle="Users"
        tableBtnTitle="New User"
        tableForm="userForm"
        :tableBtnDisable="false"
        tableViewComponent="user"
    >
        <template v-slot:item.email="{ item }">
            <a :href="'mailto:' + item.email">{{ item.email }}</a>
        </template>
        <template v-slot:item.phone="{ item }">
            <a :href="'tel:' + item.phone">{{ item.phone }}</a>
        </template>
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
                    text: "Email",
                    value: "email"
                },
                {
                    text: "Phone",
                    value: "phone"
                },
                {
                    text: "Role",
                    value: "roles[0].name"
                },
                {
                    text: "Created_at",
                    value: "created_at"
                },
                {
                    text: "Updated_at",
                    value: "updated_at"
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
