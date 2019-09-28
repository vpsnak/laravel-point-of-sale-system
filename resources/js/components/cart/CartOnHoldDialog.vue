<template>
	<v-dialog
		v-model="show"
		width="500"
	>
		<restoreCartDialog />
	</v-dialog>
</template>

<script>
	export default {
		props: {
			show: Boolean
		}
	};
</script>