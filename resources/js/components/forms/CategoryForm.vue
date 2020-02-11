<template>
    <ValidationObserver v-slot="{ invalid }">
        <v-form @submit.prevent="submit">
            <ValidationProvider
                rules="required|min:3|max:191"
                v-slot="{ errors, valid }"
                name="Name"
            >
                <v-text-field
                    :readonly="$props.readonly"
                    :error-messages="errors"
                    :success="valid"
                    v-model="formFields.name"
                    label="Name"
                    required
                ></v-text-field>
            </ValidationProvider>
            <v-switch
                :readonly="$props.readonly"
                v-model="formFields.in_product_listing"
                label="In Product listing"
            ></v-switch>

            <v-row v-if="!$props.readonly">
                <v-col cols="12" align="center" justify="center">
                    <v-btn
                        class="mr-4"
                        type="submit"
                        @click.prevent="submit"
                        :loading="loading"
                        :disabled="invalid || loading"
                        color="secondary"
                        >submit</v-btn
                    >
                    <v-btn v-if="!model" @click="clear" color="orange"
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
                name: "",
                in_product_listing: false
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
                model: "categories",
                data: { ...this.formFields }
            };
            this.create(payload)
                .then(() => {
                    this.$emit("submit", {
                        action: "paginate",
                        notification: {
                            msg: "Category added successfully",
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
