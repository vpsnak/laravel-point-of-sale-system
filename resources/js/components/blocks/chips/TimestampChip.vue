<template>
  <div>
    <h5 v-if="$props.title">{{ $props.title }}</h5>
    <v-chip pill :small="small">
      <v-icon left>{{ $props.icon }}</v-icon>
      <b>{{ $props.timestamp }}</b>
    </v-chip>
  </div>
</template>
<script>
export default {
  props: {
    small: Boolean,
    title: String,
    icon: String,
    timestamp: String
  }
};
</script>
