<template>
    <ValidationObserver v-slot="{ invalid }" ref="locationObs">
        <v-form @submit="submit">
            <div class="text-center">
                <v-chip color="blue-grey" label>
                    <v-icon left>mdi-airplane-landing</v-icon>Location Form
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
            defaultValues: {},
            formFields: {
                name: null
            }
        };
    },
    mounted() {
        this.getAllTaxes();
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
                model: "locations",
                data: { ...this.formFields }
            };
            this.create(payload)
                .then(() => {
                    this.$emit("submit", {
                        getRows: true,
                        model: "locations",
                        notification: {
                            msg: "Location added successfully",
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
