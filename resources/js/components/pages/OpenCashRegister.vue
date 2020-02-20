<template>
  <v-container class="fill-height">
    <v-row align="center" justify="center">
      <v-col cols="12" :md="8" :xl="4" :lg="6">
        <v-card class="elevation-12 mx-auto pa-2">
          <v-card-title>
            <v-icon class="mr-2">
              mdi-cash-register
            </v-icon>
            Select cash register
            <v-spacer />
            <v-tooltip bottom color="red">
              <template v-slot:activator="{ on }">
                <v-btn @click="$router.go(-1)" color="red" icon v-on="on">
                  <v-icon>
                    mdi-close
                  </v-icon>
                </v-btn>
              </template>
              <span>Close</span>
            </v-tooltip>
          </v-card-title>
          <v-card-text>
            <openCashRegisterForm @submit="submit"></openCashRegisterForm>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  methods: {
    submit(event) {
      if (event) {
        if (this.$route.query.redirect) {
          this.$router.replace(this.$route.query.redirect);
        } else {
          this.$router.go(-1);
        }
      }
    }
  }
};
</script>
