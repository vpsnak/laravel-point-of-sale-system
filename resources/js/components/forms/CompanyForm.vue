<template>
    <ValidationObserver v-slot="{ invalid }">
        <v-form @submit.prevent="submit">
            <ValidationProvider
                rules="required|max:191"
                v-slot="{ errors, valid }"
                name="Names"
            >
                <v-text-field
                    :readonly="$props.readonly"
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
                    :readonly="$props.readonly"
                    v-model="formFields.tax_number"
                    label="Tax number"
                    :error-messages="errors"
                    :success="valid"
                ></v-text-field>
            </ValidationProvider>
            <v-row v-if="!$props.readonly">
                <v-col cols="12" align="center" justify="center">
                    <v-btn
                        color="secondary"
                        class="mr-4"
                        type="submit"
                        :loading="loading"
                        :disabled="invalid || loading"
                        >submit</v-btn
                    >
                    <v-btn color="orange" v-if="!model" @click="clear"
                        >clear</v-btn
                    >
                </v-col>
            </v-row>
        </v-form>
    </ValidationObserver>
</template>
<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Object,
        readonly: Boolean
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
        ...mapActions({
            create: "create"
        }),

        submit() {
            this.loading = true;
            let payload = {
                model: "companies",
                data: { ...this.formFields }
            };
            this.create(payload)
                .then(() => {
                    this.$emit("submit", {
                        action: "paginate",
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
        }
    },
    beforeDestroy() {
        this.$off("submit");
    }
};
</script>
