<template>
    <prop-data-table
        :tableHeaders="headers"
        data-url="store-pickups"
        tableTitle="Store pickups"
        tableBtnTitle="New Store Pickup"
        tableForm="storePickupForm"
        tableViewComponent="storePickup"
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
                    text: "Street",
                    value: "street"
                },
                {
                    text: "Street 1",
                    value: "street1"
                },
                {
                    text: "Country ID",
                    value: "country_id"
                },
                {
                    text: "Region",
                    value: "region.default_name"
                },
                { text: "Actions", value: "action" }
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
