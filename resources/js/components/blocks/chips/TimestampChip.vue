<template>
  <div>
    <h5 v-if="$props.title">{{ $props.title }}</h5>
    <v-chip pill>
      <v-icon left>{{ $props.icon }}</v-icon>
      {{ $props.timestamp }}
    </v-chip>
  </div>
</template>
<script>
export default {
  props: {
    title: String,
    icon: String,
    timestamp: String
  }
};
</script>
