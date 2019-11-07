<template>
	<v-container fluid>
		<v-slide-group active-class="success" show-arrows>
			<v-slide-item>
				<v-card width="400px" class="ma-4">
					<v-card-text>
						<customerForm></customerForm>
					</v-card-text>
				</v-card>
			</v-slide-item>
			<v-slide-item>
				<v-card width="400px" class="ma-4">
					<v-card-text>
						<addressForm></addressForm>
						<userForm class="mt-6"></userForm>
					</v-card-text>
				</v-card>
			</v-slide-item>
			<v-slide-item>
				<v-card width="400px" class="ma-4">
					<v-card-text>
						<cashRegisterForm></cashRegisterForm>
						<taxForm class="mt-6"></taxForm>
						<couponForm class="mt-6"></couponForm>
					</v-card-text>
				</v-card>
			</v-slide-item>
			<v-slide-item>
				<v-card width="400px" class="ma-4">
					<v-card-text>
						<productForm></productForm>
						<categoryForm class="mt-6"></categoryForm>
					</v-card-text>
				</v-card>
			</v-slide-item>
			<v-slide-item>
				<v-card width="400px" class="ma-4">
					<v-card-text>
						<storeForm></storeForm>
						<giftCardForm class="mt-6"></giftCardForm>
					</v-card-text>
				</v-card>
			</v-slide-item>
			<v-slide-item>
				<v-card width="400px" class="ma-4">
					<v-card-text>
						<dummyProductForm></dummyProductForm>
					</v-card-text>
				</v-card>
			</v-slide-item>
		</v-slide-group>
	</v-container>
</template>