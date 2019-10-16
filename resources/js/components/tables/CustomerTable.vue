<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="customers"
        tableTitle="Customers"
        tableBtnTitle="New Customer"
        tableForm="customerForm"
        :tableBtnDisable="false"
        tableViewComponent="customer"
    >
        <template v-slot:item.email="{item}">
            <a :href="'mailto:' + item.email">{{ item.email }}</a>
        </template>
        <template v-slot:item.phone="{item}">
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
