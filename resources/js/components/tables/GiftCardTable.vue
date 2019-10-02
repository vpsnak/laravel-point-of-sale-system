<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="gift-cards"
        tableTitle="Gift Cards"
        tableBtnTitle="New Gift Card"
        tableForm="giftCardForm"
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
                    text: "Code",
                    value: "code"
                },
                {
                    text: "Enabled",
                    value: "emabled"
                },
                {
                    text: "Amount",
                    value: "amount"
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
