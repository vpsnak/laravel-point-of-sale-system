<template>
    <prop-data-table
        :tableHeaders="headers"
        dataUrl="products"
        tableTitle="Products"
    />
</template>

<script>
  import { mapMutations } from 'vuex'

  export default {
    data() {
        return {
            headers: [
                {
                    text: "Products",
                    align: "left",
                    sortable: false,
                    value: "name"
                },
                { text: "Id", value: "id" },
                { text: "Sku", value: "sku" },
                { text: "Photo url", value: "photo_url" },
              {text: 'Price', value: 'price.amount'},
              {text: 'Actions', value: 'action', sortable: false}
            ],
        }
    },
    mounted () {
      this.setRows([])
    },
    methods: {
      ...mapMutations('datatable', {
        setRows: 'setRows'
      })
    }
  }
</script>
