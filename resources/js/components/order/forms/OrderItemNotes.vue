<template>
  <v-container fluid class="fill-height">
    <v-row no-gutters>
      <v-col :cols="12">
        <v-textarea :value="model" rows="7" outlined></v-textarea>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
export default {
  props: {
    model: String
  }
};
</script>
