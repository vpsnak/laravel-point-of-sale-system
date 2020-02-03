<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="cash-registers"
        tableTitle="Cash Registers"
        tableBtnTitle="New Cash Register"
        tableForm="cashRegisterForm"
        tableViewComponent="cashRegister"
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
                    text: "Store",
                    value: "store.name"
                },
                {
                    text: "Barcode",
                    value: "barcode"
                },
                {
                    text: "Is open",
                    value: "is_open"
                },

                { text: "Actions", value: "action" }
            ]
        };
    }
};
</script>
