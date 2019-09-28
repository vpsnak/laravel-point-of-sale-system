<template>
	<v-card>
		<v-divider></v-divider>
		<v-card-actions>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" text @click="close">Close</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
	import { mapActions } from "vuex";

	export default {
		computed: {},
		methods: {}
	};
</script>
