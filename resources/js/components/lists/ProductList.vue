<template>
	<v-card>
		<v-card-text>
			<v-row>
				<v-col>
					<v-toolbar flat>
						<v-text-field placeholder="Search product" class="grey--text">
							<v-icon slot="prepend">mdi-magnify</v-icon>
						</v-text-field>
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn icon v-on="on" class="ml-1">
									<v-icon>add</v-icon>
								</v-btn>
							</template>
							<span>Add product</span>
						</v-tooltip>
					</v-toolbar>
				</v-col>
			</v-row>
			<v-row>
				<v-col v-for="card in cards" :key="card.id" :cols="card.flex">
					<v-card>
						<v-img
							:src="card.src"
							class="white--text"
							height="200px"
							gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
						>
							<v-card-title class="fill-height align-end" v-text="card.title"></v-card-title>
						</v-img>

						<v-card-actions>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon v-on="on" class="ml-1">
										<v-icon>add_shopping_cart</v-icon>
									</v-btn>
								</template>
								<span>Add to shopping cart</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon v-on="on" class="ml-1">
										<v-icon>mdi-heart</v-icon>
									</v-btn>
								</template>
								<span>Add to favorites</span>
							</v-tooltip>
							<div class="flex-grow-1"></div>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon v-on="on" class="ml-1">
										<v-icon>remove_red_eye</v-icon>
									</v-btn>
								</template>
								<span>View product</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon v-on="on" class="ml-1">
										<v-icon>edit</v-icon>
									</v-btn>
								</template>
								<span>Edit product</span>
							</v-tooltip>
						</v-card-actions>
					</v-card>
				</v-col>
			</v-row>
		</v-card-text>
		<v-card-actions></v-card-actions>
	</v-card>
</template>

<script>
export default {
	data() {
		return {
			cards: [
				{
					id: "1",
					title: "Pre-fab homes",
					src: "https://cdn.vuetifyjs.com/images/cards/house.jpg",
					flex: 3
				},
				{
					id: "2",
					title: "Favorite road trips",
					src: "https://cdn.vuetifyjs.com/images/cards/road.jpg",
					flex: 3
				},
				{
					id: "3",
					title: "Best airlines",
					src: "https://cdn.vuetifyjs.com/images/cards/plane.jpg",
					flex: 3
				},
				{
					id: "4",
					title: "Favorite road trips",
					src: "https://cdn.vuetifyjs.com/images/cards/road.jpg",
					flex: 3
				}
			]
		};
	}
};
</script>