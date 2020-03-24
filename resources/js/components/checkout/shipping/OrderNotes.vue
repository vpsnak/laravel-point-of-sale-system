<template>
  <v-col :cols="12">
    <v-textarea
      :readonly="$props.readonly"
      label="Notes"
      prepend-inner-icon="mdi-card-text-outline"
      v-model="delivery.notes"
      counter
      clearable
      no-resize
      outlined
    ></v-textarea>
  </v-col>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
  props: {
    readonly: Boolean
  },

  computed: {
    ...mapState("cart", ["delivery"]),

    notes: {
      get() {
        return this.delivery.notes;
      },
      set(value) {
        this.setNotes(value);
      }
    }
  },
  methods: {
    ...mapMutations("cart", ["setNotes"])
  }
};
</script>
