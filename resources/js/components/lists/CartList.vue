<template>
    <v-card>
		<v-card-text>
		    <v-row>
			    <v-col>
				    <v-toolbar flat>
					    <v-text-field placeholder="Search customer" class="grey--text">
						    <v-icon slot="prepend">mdi-magnify</v-icon>
						</v-text-field>
						<v-tooltip bottom>
						    <template v-slot:activator="{ on }">
							    <v-btn icon v-on="on" class="ml-1">
								    <v-icon>
									    person_add
									</v-icon>
								</v-btn>
							</template>
							<span>Add customer</span>
						</v-tooltip>
					</v-toolbar>
					<v-divider />
					    <v-list three-line subheader dense>
						    <v-subheader>Shopping cart</v-subheader>
                                <v-list-item
								    v-for="item in items"
									:key="item.name"
									@click=""
									dense
								>
												
								<v-list-item-content>
								    <v-list-item-title v-text="item.name"></v-list-item-title>
									<v-list-item-subtitle>$ {{ item.price }}</v-list-item-subtitle>
								</v-list-item-content>

								<v-list-item-action>
								    <v-btn icon>
									    <v-icon color="grey lighten-1">remove</v-icon>
									</v-btn>
								</v-list-item-action>

								<v-list-item-action>
									<v-chip filter>
										<span>42</span>
									</v-chip>
								</v-list-item-action>

								<v-list-item-action>
								    <v-btn icon>
									    <v-icon color="grey lighten-1">add</v-icon>
									</v-btn>
								</v-list-item-action>

								<v-list-item-action>
								    <v-btn icon>
									    <v-icon color="grey lighten-1">delete</v-icon>
									</v-btn>
								</v-list-item-action>
							</v-list-item>
						</v-list>
					</v-col>
				</v-row>

					<v-row>
						<v-col>
						    <v-divider />

							<div class="d-flex justify-space-between">
								<span class="pa-2">Sub total</span>
							    <span class="pa-2">12 {{ }}</span>
							</div>

							<v-divider />

							<div class="d-flex justify-space-between">
								<span class="pa-2">Tax</span>
							    <span class="pa-2">12 {{ }}</span>
							</div>

							<v-divider />

							<div class="d-flex justify-space-between">
								<span class="pa-2">Total discount</span>
							    <span class="pa-2">12 {{ }}</span>
                            </div>

							<v-divider />

							<div class="d-flex justify-space-between">
								<span class="pa-2">Total</span>
							    <span class="pa-2">12 {{ }}</span>
						    </div>

						<v-divider />
										
						<v-btn block class="my-2">
						    Checkout
					    </v-btn>
										
				    <v-divider />
			    </v-col>
			</v-row>
			<v-row>
				<v-col cols="4" class="text-center">
					<v-btn icon>
						<v-icon>
						    fa-recycle
					    </v-icon>
				    </v-btn>
				</v-col>

				<v-col cols="4" class="text-center">
					<v-btn icon>
						<v-icon>
						    pause
					    </v-icon>
				    </v-btn>
				</v-col>

				<v-col cols="4" class="text-center">
					<v-btn icon>
						<v-icon>
						    delete
					    </v-icon>
				    </v-btn>
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
            items: [
				{ name: 'Cotton Long Sleeve T-shirt', price: '44' },
				{ name: 'Recipes', price: '4' },
				{ name: 'Work', price: '7' },
			],
        }
    }
}
</script>