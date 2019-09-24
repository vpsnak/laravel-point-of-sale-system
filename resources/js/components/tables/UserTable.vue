<template>
    <v-card>
        <v-card-title>
            {{ this.title }}
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
        </v-card-title>
        <v-data-table :headers="headers" :items="data" :search="search" :loading="loading">
            <template v-slot:item.email="{item}">
                <a :href="'mailto:' + item.email">{{ item.email }}</a>
            </template>
            <v-alert
                slot="no-results"
                :value="true"
                color="error"
                icon="warning"
            >Your search for "{{ search }}" found no results.
            </v-alert>
        </v-data-table>
    </v-card>
</template>


<script>
  import { mapActions, mapMutations, mapState } from 'vuex'

  export default {
    // data () {
    // return {
    //   loader: false,
    //   disableFilters: false,
    //   model: 'users',
    search: '',
    //   headers: [
    //     {
    //       text: 'Id',
    //       value: 'id'
    //     },
    //     {
    //       text: 'Name',
    //       value: 'name'
    //     },
    //     {
    //       text: 'E-mail',
    //       value: 'email',
    //       sortable: false
    //     },
    //     {
    //       text: 'E-mail verified',
    //       value: 'email_verified_at',
    //       sortable: false
    //     },
    //     {
    //       text: 'Password',
    //       value: 'password',
    //       sortable: false
    //     }
    //   ]
    // }
    // },
    mounted () {
      this.setHeaders([
        {
          text: 'Id',
          value: 'id'
        },
        {
          text: 'Name',
          value: 'name'
        },
        {
          text: 'E-mail',
          value: 'email',
          sortable: false
        },
        {
          text: 'E-mail verified',
          value: 'email_verified_at',
          sortable: false
        },
        {
          text: 'Password',
          value: 'password',
          sortable: false
        }
      ])
      this.getData()
    },
    computed: {
      ...mapState('datatable', {
        title: 'title',
        headers: 'headers',
        data: 'data',
        loading: 'loading',
      })
    },
    methods: {
      applyFilter (filter) {},
      ...mapActions('datatable', {
        getData: 'getData'
      }),
      ...mapMutations('datatable', {
        setHeaders: 'setHeaders'
      })
    }
  }
</script>
