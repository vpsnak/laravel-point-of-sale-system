<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="products"
        tableTitle="Products"
        tableBtnTitle="New Product"
        tableForm="productForm"
        :tableBtnDisable="cashierDisabled()"
        tableViewComponent="product"
    >
        <template v-slot:item.final_price="{ item }"
            >{{ parseFloat(item.final_price).toFixed(2) }} $</template
        >
        <template v-slot:item.stock="{ item }">
            <v-avatar
                v-if="item.stock <= 10 && item.stock > 0"
                color="orange"
                size="36"
            >
                {{ item.stock }}
            </v-avatar>
            <v-avatar v-else-if="item.stock <= 0" color="red" size="36">
                {{ item.stock }}
            </v-avatar>
            <v-avatar v-else size="36">{{ item.stock }}</v-avatar>
        </template>
    </prop-data-table>
</template>
<script>
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
                { text: "Actions", value: "action" }
            ]
        };
    },
    computed: {
        role() {
            return this.$store.getters.role;
        }
    },
    methods: {
        cashierDisabled() {
            if (this.role == "admin" || this.role == "store_manager") {
                return false;
            } else {
                return true;
            }
        }
    }
};
</script>
