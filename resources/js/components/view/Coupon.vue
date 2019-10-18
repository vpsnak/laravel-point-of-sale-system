<template>
    <v-container v-if="couponData">
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>{{couponData.name}}</v-card-title>
                    <v-card-text>
                        <div class="subtitle-1">Code: {{couponData.code}}</div>
                        <div class="subtitle-1">Uses: {{couponData.uses}}</div>
                        <div class="subtitle-1">Discount ID: {{couponData.discount_id}}</div>
                        <div class="subtitle-1">From: {{couponData.from}}</div>
                        <div class="subtitle-1">To: {{couponData.to}}</div>
                        <div class="subtitle-1">Created at: {{couponData.created_at}}</div>
                        <div class="subtitle-1">Updated at: {{couponData.updated_at}}</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="8" v-if="couponData.discounts.length > 0">
                <v-card>
                    <v-card-title>Cash Registers</v-card-title>
                    <v-card-text>
                        <v-simple-table dense>
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <th class="text-left">Type</th>
                                        <th class="text-left">Amount</th>
                                        <th class="text-left">Created at</th>
                                        <th class="text-left">Updated at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="discount in couponData.discounts" :key="discount.id">
                                        <td>{{ discount.type }}</td>
                                        <td>{{ discount.amount }} $</td>
                                        <td>{{ discount.created_at }}</td>
                                        <td>{{ discount.updated_at }}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="8" v-else>
                <v-card-title>Discounts</v-card-title>
                <v-card-text>There are no discount for this coupon</v-card-text>
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
            coupon: null
        };
    },
    mounted() {
        if (this.model)
            this.getOne({
                model: "coupons",
                data: {
                    id: this.model.id
                }
            }).then(result => {
                this.coupon = result;
            });
    },
    computed: {
        couponData() {
            return this.coupon;
        }
    },
    methods: {
        ...mapActions({
            getOne: "getOne"
        })
    }
};
</script>
