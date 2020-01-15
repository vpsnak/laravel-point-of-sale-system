<template>
    <ValidationObserver v-slot="{ invalid }" ref="couponObs">
        <v-form @submit.prevent="submit">
            <div class="text-center">
                <v-chip color="primary" label>
                    <v-icon left>fas fa-ticket-alt</v-icon>Coupon Form
                </v-chip>
            </div>
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="Name"
            >
                <v-text-field
                    v-model="formFields.name"
                    label="Name"
                    :disabled="loading"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="Code"
            >
                <v-text-field
                    v-model="formFields.code"
                    label="Code"
                    :disabled="loading"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required|numeric|max:11"
                v-slot="{ errors, valid }"
                name="Uses"
            >
                <v-text-field
                    v-model="formFields.uses"
                    type="number"
                    label="Uses"
                    :disabled="loading"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Discount type"
            >
                <v-select
                    v-model="formFields.discount.type"
                    label="Discount type"
                    :items="discount_types"
                    :disabled="loading"
                    item-value="id"
                    :error-messages="errors"
                    :success="valid"
                ></v-select>
            </ValidationProvider>
            <ValidationProvider
                v-if="formFields.discount.type == 'flat'"
                :rules="{
                    required: true,
                    max_value: 99999,
                    regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g
                }"
                v-slot="{ errors, valid }"
                name="Discount amount"
            >
                <v-text-field
                    v-model="formFields.discount.amount"
                    type="number"
                    label="Discount amount"
                    :disabled="loading"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>

            <ValidationProvider
                v-if="formFields.discount.type == 'percentage'"
                :rules="{
                    required: true,
                    max_value: 99,
                    regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g
                }"
                v-slot="{ errors, valid }"
                name="Discount amount"
            >
                <v-text-field
                    v-model="formFields.discount.amount"
                    type="number"
                    label="Discount amount"
                    :disabled="loading"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>

            <v-menu
                v-model="menu1"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="290px"
            >
                <template v-slot:activator="{ on }">
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Names"
                    >
                        <v-text-field
                            v-model="fromDate"
                            label="From:"
                            prepend-icon="event"
                            :error-messages="errors"
                            :success="valid"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </ValidationProvider>
                </template>
                <v-date-picker
                    v-model="formFields.from"
                    @input="menu1 = false"
                ></v-date-picker>
            </v-menu>
            <v-menu
                v-model="menu2"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="290px"
            >
                <template v-slot:activator="{ on }">
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Names"
                    >
                        <v-text-field
                            v-model="toDate"
                            label="To:"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                </template>
                <v-date-picker
                    v-model="formFields.to"
                    @input="menu2 = false"
                ></v-date-picker>
            </v-menu>

            <v-btn
                class="mr-4"
                type="submit"
                :loading="loading"
                :disabled="invalid || loading"
                >submit</v-btn
            >
            <v-btn v-if="!model" @click="clear">clear</v-btn>
        </v-form>
    </ValidationObserver>
</template>
<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Object || undefined
    },
    data() {
        return {
            loading: false,
            defaultValues: {},
            discount_types: ["flat", "percentage"],
            formFields: {
                name: null,
                code: null,
                uses: null,
                discount: {
                    type: null,
                    amount: null
                },
                from: null,
                to: null
            },
            menu1: false,
            menu2: false
        };
    },
    mounted() {
        this.defaultValues = { ...this.formFields };
        if (this.$props.model) {
            this.formFields = {
                ...this.$props.model
            };
        }
    },
    computed: {
        fromDate() {
            if (this.formFields.from) {
                return this.parseDate(this.formFields.from);
            } else {
                return null;
            }
        },
        toDate() {
            if (this.formFields.to) {
                return this.parseDate(this.formFields.to);
            } else {
                return null;
            }
        }
    },
    methods: {
        parseDate(d) {
            if (d.length) {
                let [year, month, day] = d.split("-");
                return `${month}/${day}/${year}`;
            } else {
                return null;
            }
        },
        submit() {
            this.loading = true;
            let payload = {
                model: "coupons",
                data: { ...this.formFields }
            };
            this.create(payload)
                .then(response => {
                    this.$emit("submit", {
                        getRows: true,
                        model: "coupons",
                        notification: {
                            msg: response.info,
                            type: "success"
                        }
                    });
                    this.clear();
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        clear() {
            this.formFields = { ...this.defaultValues };
        },
        ...mapActions({
            getAll: "getAll",
            getOne: "getOne",
            create: "create",
            delete: "delete"
        })
    },
    beforeDestroy() {
        this.$off("submit");
    }
};
</script>
