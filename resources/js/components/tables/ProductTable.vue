<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="products"
        tableTitle="Products"
        tableBtnTitle="New Product"
        tableForm="productForm"
        :tableBtnDisable="false"
        tableViewComponent="product"
    />
</template>
<script>
import { mapMutations } from "vuex";

export default {
    data() {
        return {
            headers: [
                { text: "Id", value: "id" },
                { text: "Name", value: "name" },
                { text: "Sku", value: "sku" },
                // { text: "Photo url", value: "photo_url" },
                // { text: "Price", value: "price.amount" },
                { text: "Price", value: "final_price" },
                { text: "Stock", value: "stock" },
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
