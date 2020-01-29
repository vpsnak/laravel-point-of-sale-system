<template>
    <ValidationObserver v-slot="{ invalid }" ref="giftcardObs">
        <v-form @submit.prevent="submit">
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="Names"
            >
                <v-text-field
                    v-model="formFields.name"
                    label="Name"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="Tax number"
            >
                <v-text-field
                    v-model="formFields.tax_number"
                    label="Tax number"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
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
            defaultValues: {},
            formFields: {
                name: null,
                tax_number: null,
                logo: null
            }
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
    methods: {
        submit() {
            this.loading = true;
            let payload = {
                model: "companies",
                data: { ...this.formFields }
            };
            this.create(payload)
                .then(() => {
                    this.$emit("submit", {
                        getRows: true,
                        model: "companies",
                        notification: {
                            msg: "Company added successfully",
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
