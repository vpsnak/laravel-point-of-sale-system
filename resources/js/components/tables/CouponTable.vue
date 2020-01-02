<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="coupons"
        tableTitle="Coupons"
        tableBtnTitle="New Coupon"
        tableForm="couponForm"
        tableViewComponent="coupon"
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
                    text: "Uses",
                    value: "uses"
                },
                {
                    text: "Discount Type",
                    value: "discount.type"
                },
                {
                    text: "Discount Amount",
                    value: "discount.amount"
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
            setRows: "setRows"
        })
    }
};
</script>
