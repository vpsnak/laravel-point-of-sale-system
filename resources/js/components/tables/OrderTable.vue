<template>
    <prop-data-table
        :tableHeaders="headers"
        dataUrl="orders"
        tableTitle="Orders"
    />
</template>

<script>
  import { mapMutations } from 'vuex'

  export default {
    data () {
      return {
				headers: [
					{
						text: "Orders",
						align: "left",
						sortable: false,
						value: "name"
					},
					{ text: "Calories", value: "calories" },
					{ text: "Fat (g)", value: "fat" },
					{ text: "Carbs (g)", value: "carbs" },
					{ text: "Protein (g)", value: "protein" },
                  {text: 'Iron (%)', value: 'iron'},
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
