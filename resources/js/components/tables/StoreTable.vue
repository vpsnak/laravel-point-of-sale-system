<template>
    <prop-data-table
        :tableHeaders="headers"
        dataUrl="stores"
        tableTitle="Stores"
    />
</template>

<script>
  import { mapMutations } from 'vuex'

  export default {
    data () {
      return {
        headers: [
          {
            text: 'Id',
            value: 'id'
          },
          {
            text: 'Name',
            value: 'name'
          },
          {
            text: 'Address Id',
            value: 'address_id'
          },
          {
            text: 'Actions',
            value: 'action',
            sortable: false
          }
        ]
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
