<template>
	<v-container fluid>
		<v-row>
			<v-col cols="3">
				<customerForm></customerForm>
			</v-col>
			<v-col cols="3">
				<addressForm></addressForm>
				<cashRegisterForm class="mt-6"></cashRegisterForm>
			</v-col>
			<v-col cols="3">
				<productForm></productForm>
				<categoryForm class="mt-6"></categoryForm>
				<storeForm class="mt-6"></storeForm>
			</v-col>
			<v-col cols="3">
				<userForm></userForm>
				<taxForm class="mt-6"></taxForm>
			</v-col>
		</v-row>
	</v-container>
</template>