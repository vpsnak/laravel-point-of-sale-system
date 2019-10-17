<template>
    <v-container v-if="giftCardData">
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{giftCardData.name}}</v-card-title>
                    <v-card-text>
                        <div class="subtitle-1">Code: {{giftCardData.code}}</div>
                        <div class="subtitle-1">Is enabled: {{giftCardData.enabled ? 'Yes' : 'No'}}</div>
                        <div class="subtitle-1">Amount: {{giftCardData.amount}} $</div>
                        <div class="subtitle-1">Created at: {{giftCardData.created_at}}</div>
                        <div class="subtitle-1">Updated at: {{giftCardData.updated_at}}</div>
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
            giftCard: null
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
                this.giftCard = result;
            });
    },
    computed: {
        giftCardData() {
            return this.giftCard;
        }
    },
    methods: {
        ...mapActions({
            getOne: "getOne"
        })
    }
};
</script>
