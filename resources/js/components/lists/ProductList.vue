<template>
	<v-card>
		<v-card-text>
			<v-row>
				<v-col>
					<v-toolbar flat>
						<v-text-field
							placeholder="Search product"
							class="grey--text"
						>
							<v-icon slot="prepend">mdi-magnify</v-icon>
						</v-text-field>
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn
									icon
									v-on="on"
									class="ml-1"
								>
									<v-icon>add</v-icon>
								</v-btn>
							</template>
							<span>Add product</span>
						</v-tooltip>
					</v-toolbar>
				</v-col>
			</v-row>
			<v-row>
				<v-col
					cols="12"
					sm="6"
					class="py-2"
				>
					<v-btn-toggle>
						<v-btn
							:loading="loading"
							:disabled="loading"
							@click="loader = 'loading'"
							text
							value="left"
						>
							<v-icon left>fas fa-heart</v-icon>
							<span>Favourites</span>
						</v-btn>
						<v-btn
							:loading="loading2"
							:disabled="loading2"
							@click="loader = 'loading2'"
							text
							value="center"
						>
							<v-icon left>fas fa-sort-numeric-up</v-icon>
							<span class="hidden-sm-and-down">Best Sellers</span>
						</v-btn>
						<v-btn
							:loading="loading3"
							:disabled="loading3"
							@click="loader = 'loading3'"
							text
							value="right"
						>
							<v-icon left>fas fa-spa</v-icon>
							<span class="hidden-sm-and-down">Roses</span>
						</v-btn>
						<v-btn
							:loading="loading4"
							:disabled="loading4"
							@click="loader = 'loading4'"
							text
							value="justify"
						>
							<v-icon left>fas fa-seedling</v-icon>
							<span class="hidden-sm-and-down">Succulents</span>
						</v-btn>
					</v-btn-toggle>
				</v-col>
			</v-row>
			<v-row>
				<v-col
					v-for="card in cards"
					:key="card.id"
					:cols="card.flex"
				>
					<v-card>
						<v-img
							:src="card.src"
							class="white--text"
							height="200px"
							gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
						>
							<v-card-title
								class="fill-height align-end"
								v-text="card.title"
							></v-card-title>
						</v-img>

						<v-card-actions>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn
										icon
										v-on="on"
										class="ml-1"
									>
										<v-icon>add_shopping_cart</v-icon>
									</v-btn>
								</template>
								<span>Add to shopping cart</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn
										icon
										v-on="on"
										class="ml-1"
									>
										<v-icon>mdi-heart</v-icon>
									</v-btn>
								</template>
								<span>Add to favorites</span>
							</v-tooltip>
							<div class="flex-grow-1"></div>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn
										icon
										v-on="on"
										class="ml-1"
									>
										<v-icon>remove_red_eye</v-icon>
									</v-btn>
								</template>
								<span>View product</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn
										icon
										v-on="on"
										class="ml-1"
									>
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
				loader: null,
				loading: false,
				loading2: false,
				loading3: false,
				loading4: false,
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
		},
		watch: {
			loader() {
				const l = this.loader;
				this[l] = !this[l];

				setTimeout(() => (this[l] = false), 2000);

				this.loader = null;
			}
		}
	};
</script>