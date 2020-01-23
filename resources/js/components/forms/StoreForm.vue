<template>
    <ValidationObserver v-slot="{ invalid }" ref="storeObs">
        <v-form @submit="submit">
            <div class="text-center">
                <v-chip color="blue-grey" label>
                    <v-icon left>fas fa-warehouse</v-icon>Store Form
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
                :rules="{
                    required: true,
                    min: 8,
                    max: 100,
                    regex: /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g
                }"
                v-slot="{ errors, valid }"
                name="Phone"
            >
                <v-text-field
                    v-model="formFields.phone"
                    label="Phone"
                    :min="0"
                    :disabled="loading"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="Street"
            >
                <v-text-field
                    v-model="formFields.street"
                    label="Street"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="Postal code"
            >
                <v-text-field
                    v-model="formFields.postcode"
                    label="Postcode"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="City"
            >
                <v-text-field
                    v-model="formFields.city"
                    label="City"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Companies"
            >
                <v-select
                    v-model="formFields.company_id"
                    :items="companies"
                    label="Companies"
                    :disabled="loading"
                    item-text="name"
                    item-value="id"
                    :error-messages="errors"
                    :success="valid"
                ></v-select>
            </ValidationProvider>
            <ValidationProvider
                rules="required"
                v-slot="{ errors, valid }"
                name="Taxes"
            >
                <v-select
                    v-model="formFields.tax_id"
                    :items="taxes"
                    label="Taxes"
                    :disabled="loading"
                    item-text="name"
                    item-value="id"
                    :error-messages="errors"
                    :success="valid"
                ></v-select>
            </ValidationProvider>
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
            valid: true,
            taxes: [],
            companies: [],
            defaultValues: {},
            formFields: {
                name: null,
                phone: null,
                street: null,
                postcode: null,
                city: null,
                tax_id: null,
                company_id: null,
                taxable: false,
                is_default: false,
                created_by: null
            }
        };
    },
    mounted() {
        this.getAllTaxes();
        this.getAllCompanies();
        this.defaultValues = { ...this.formFields };
        if (this.$props.model) {
            this.formFields = {
                ...this.$props.model
            };
        }
    },
    computed: {
        user_id: {
            get() {
                return this.$store.state.user.id;
            }
        }
    },
    methods: {
        submit() {
            this.loading = true;
            let payload = {
                model: "stores",
                data: { ...this.formFields }
            };
            this.create(payload)
                .then(() => {
                    this.$emit("submit", {
                        getRows: true,
                        model: "stores",
                        notification: {
                            msg: "Store added successfully",
                            type: "success"
                        }
                    });
                })
                .finally(() => {
                    this.clear();
                    this.loading = false;
                });
        },
        clear() {
            this.formFields = this.defaultValues;
        },
        getAllTaxes() {
            this.loading = true;
            this.getAll({
                model: "taxes"
            })
                .then(response => {
                    this.taxes = response;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        getAllCompanies() {
            this.loading = true;
            this.getAll({
                model: "companies"
            })
                .then(response => {
                    this.companies = response;
                })
                .finally(() => {
                    this.loading = false;
                });
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
