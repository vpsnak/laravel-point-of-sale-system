<template>
  <div class="text-center">
    <v-menu offset-y>
      <template v-slot:activator="{ on }">
        <v-chip color="primary" dark v-on="on">
          <v-icon left>
            mdi-table-search
          </v-icon>
          Filters
        </v-chip>
      </template>
      <v-card width="450" class="pa-5" outlined>
        <v-form @submit="submit">
          <v-btn type="submit">Apply</v-btn>
          <v-btn @click.stop="reset">Reset</v-btn>
        </v-form>
      </v-card>
    </v-menu>
  </div>
</template>

<script>
export default {
  methods: {
    submit() {},
    reset() {}
  }
};
</script>
