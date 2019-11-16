<template>
    <ValidationObserver
        v-slot="{ invalid }"
        ref="obs2"
        tag="form"
        @change="isValid()"
        @input="isValid()"
        @focus="isValid()"
        @blur="isValid()"
        @submit.prevent="submit"
    >
        <div>
            <v-row>
                <v-col cols="4">
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="First name"
                    >
                        <v-text-field
                            v-model="formFields.first_name"
                            :readonly="$props.readonly"
                            label="First name"
                            :disabled="loading"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="City"
                    >
                        <v-text-field
                            v-model="formFields.city"
                            :readonly="$props.readonly"
                            label="City"
                            :disabled="loading"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Region"
                    >
                        <v-select
                            v-model="formFields.region_id"
                            :readonly="$props.readonly"
                            :items="regions"
                            label="Regions"
                            item-text="default_name"
                            item-value="region_id"
                            :error-messages="errors"
                            :success="valid"
                        ></v-select>
                    </ValidationProvider>

                    <v-text-field
                        v-model="formFields.company"
                        :readonly="$props.readonly"
                        label="Company"
                        :disabled="loading"
                    ></v-text-field>
                </v-col>
                <v-col cols="4">
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Last Name"
                    >
                        <v-text-field
                            v-model="formFields.last_name"
                            :readonly="$props.readonly"
                            label="Last name"
                            :disabled="loading"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>

                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Post Code"
                    >
                        <v-text-field
                            :readonly="$props.readonly"
                            v-model="formFields.postcode"
                            label="Postcode"
                            :disabled="loading"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>

                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Country"
                    >
                        <v-select
                            :readonly="$props.readonly"
                            v-model="formFields.country_id"
                            :items="countries"
                            label="Countries"
                            required
                            item-text="iso2_code"
                            item-value="iso2_code"
                            :error-messages="errors"
                            :success="valid"
                        ></v-select>
                    </ValidationProvider>
                    <v-text-field
                        v-model="formFields.vat_id"
                        label="Vat id"
                        :disabled="loading"
                    ></v-text-field>
                </v-col>
                <v-col cols="4">
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Street"
                    >
                        <v-text-field
                            :readonly="$props.readonly"
                            v-model="formFields.street"
                            label="Street"
                            :disabled="loading"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Second Street"
                    >
                        <v-text-field
                            :readonly="$props.readonly"
                            v-model="formFields.street2"
                            label="Second Street"
                            :disabled="loading"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                    <ValidationProvider
                        rules="required"
                        v-slot="{ errors, valid }"
                        name="Phone"
                    >
                        <v-text-field
                            :readonly="$props.readonly"
                            v-model="formFields.phone"
                            label="Phone"
                            :disabled="loading"
                            :error-messages="errors"
                            :success="valid"
                        ></v-text-field>
                    </ValidationProvider>
                </v-col>
            </v-row>
            <v-row v-if="!$props.readonly">
                <v-spacer></v-spacer>
                <v-btn type="submit">Submit</v-btn>
            </v-row>
        </div>
    </ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Object,
        readonly: Boolean,
        isBilling: Boolean
    },
    data() {
        return {
            loading: false,
            countries: [],
            regions: [],
            default: {
                first_name: "",
                last_name: null,
                street: null,
                street2: null,
                city: null,
                country_id: null,
                region_id: null,
                postcode: null,
                phone: null,
                company: null,
                vat_id: null,
                deliverydate: null,
                shipping: false,
                billing: false
            }
        };
    },

    mounted() {
        this.getAllRegions();
        this.getAllCountries();
    },
    computed: {
        formFields: {
            get() {
                if (this.$props.model !== undefined) {
                    return this.$props.model;
                } else {
                    return this.default;
                }
            }
        }
    },
    methods: {
        submit() {},
        async isValid() {
            if (this.$refs.obs2) {
                const value = await this.$refs.obs2.validate();
                this.$store.commit("cart/setIsValid2", value);
            }
        },
        getAllRegions() {
            this.loading = true;
            this.getAll({
                model: "regions"
            })
                .then(regions => {
                    this.regions = regions;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        getAllCountries() {
            this.getAll({
                model: "countries"
            }).then(countries => {
                this.countries = countries;
            });
        },
        ...mapActions({
            getAll: "getAll",
            getOne: "getOne",
            create: "create",
            delete: "delete"
        })
    }
};
</script>
