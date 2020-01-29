<template>
    <v-container>
        <v-row v-if="taxData">
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{ taxData.name }}</v-card-title>
                    <v-card-text>
                        <div class="subtitle-1">
                            Percentage: {{ taxData.percentage }} %
                        </div>
                        <div class="subtitle-1">
                            Created at: {{ taxData.created_at }}
                        </div>
                        <div class="subtitle-1">
                            Updated at: {{ taxData.updated_at }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-else>
            <v-col cols="12" align="center" justify="center">
                <v-progress-circular
                    indeterminate
                    color="secondary"
                ></v-progress-circular>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Int32Array | null
    },
    data() {
        return {
            tax: null
        };
    },
    mounted() {
        if (this.model)
            this.getOne({
                model: "taxes",
                data: {
                    id: this.model.id
                }
            }).then(result => {
                this.tax = result;
            });
    },
    computed: {
        taxData() {
            return this.tax;
        }
    },
    methods: {
        ...mapActions({
            getOne: "getOne"
        })
    }
};
</script>
