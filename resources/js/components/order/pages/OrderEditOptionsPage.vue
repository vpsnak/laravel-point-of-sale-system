<template>
  <v-container class="fill-height">
    <v-row justify="center" align="center">
      <v-col :xl="6" :lg="8" :md="10" :cols="12">
        <v-card>
          <v-card-title>
            <v-icon class="pr-2">
              mdi-file-document-edit-outline
            </v-icon>
            Edit order options
            <v-spacer />
            <v-tooltip bottom color="red">
              <template v-slot:activator="{ on }">
                <v-btn @click.stop="back()" color="red" icon v-on="on">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </template>
              <span>Close</span>
            </v-tooltip>
          </v-card-title>
          <v-container>
            <v-row align="center" justify="space-between">
              <v-col md="auto" :cols="12">
                <customerSearch :keywordLength="3" :editable="true" />
              </v-col>
              <v-col cols="auto">
                <cartMethods :editOrder="true" />
              </v-col>
            </v-row>
          </v-container>
          <v-container style="height:60vh;overflow-y:auto;">
            <v-row justify="center" align="center">
              <shipping />
            </v-row>
          </v-container>
          <v-container>
            <v-row justify="center" align="center">
              <v-col :cols="6">
                <orderSave url="update-options" />
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
export default {
  methods: {
    back() {
      this.$router.go(-1);
    }
  }
};
</script>
