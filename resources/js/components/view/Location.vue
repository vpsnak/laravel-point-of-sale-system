<template>
    <v-container v-if="locationData">
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{ locationData.name }}</v-card-title>
                    <v-card-text>
                        <div class="subtitle-1">
                            Created at: {{ locationData.created_at }}
                        </div>
                        <div class="subtitle-1">
                            Updated at: {{ locationData.updated_at }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
    <div v-else>Loading...</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Int32Array | null
    },
    data() {
        return {
            location: null
        };
    },
    mounted() {
        if (this.model)
            this.getOne({
                model: "gift-cards",
                data: {
                    id: this.model.id
                }
            }).then(result => {
                this.location = result;
            });
    },
    computed: {
        locationData() {
            return this.location;
        }
    },
    methods: {
        ...mapActions({
            getOne: "getOne"
        })
    }
};
</script>
