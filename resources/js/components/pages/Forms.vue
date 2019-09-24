<template>
	<v-container fluid>
		<v-row>
			<v-col cols="3">
				<customerForm></customerForm>
			</v-col>
			<v-col cols="3">
				<addressForm></addressForm>
			</v-col>
			<v-col cols="3">
				<productForm></productForm>
				<categoryForm class="mt-6"></categoryForm>
				<storeForm class="mt-6"></storeForm>
			</v-col>
			<v-col cols="3">
				<userForm></userForm>
			</v-col>
		</v-row>
	</v-container>
</template>