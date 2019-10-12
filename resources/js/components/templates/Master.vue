<template>
    <v-app id="app">
        <sideMenu/>
        
        <topMenu/>
        
        <notification/>
        
        <v-content>
            <router-view></router-view>
        </v-content>
    </v-app>
</template>

<script>
  import { mapActions } from 'vuex'

  export default {
    mounted () {
      this.getOne({
        model: 'users',
        data: {
          id: 1
        }
      }).then((result) => {
        this.$store.state.user = result
      })
      this.getOne({
        model: 'stores',
        data: {
          id: 1
        }
      }).then((result) => {
        this.$store.state.store = result
      })
      this.getOne({
        model: 'cash-registers',
        data: {
          id: 1
        }
      }).then((result) => {
        this.$store.state.cashRegister = result
      })

    },
    methods: {
      ...mapActions({
        getOne: 'getOne'
      })
    }
  }
</script>

<style>
    .fas,
    .fab,
    .fa {
        font-size: 1.2em !important;
    }
    
    a {
        text-decoration: none;
    }
    html {
        overflow-y: hidden;
    }
    
    input[type="number"] {
        -moz-appearance: textfield;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
    }
</style>
