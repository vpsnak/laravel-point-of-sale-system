<template>
    <div>
        <v-form v-model="valid">
            <div class="text-center">
                <v-chip color="blue-grey" label>
                    <v-icon left>fas fa-warehouse</v-icon>Store Form
                </v-chip>
            </div>
            <v-text-field v-model="formFields.name" :counter="10" label="Name" required></v-text-field>
            <v-row justify="space-around">
                <v-switch v-model="formFields.taxable" label="Taxable"></v-switch>
                <v-switch v-model="formFields.is_default" label="Default"></v-switch>
            </v-row>
            <v-select
                v-model="formFields.tax_id"
                :items="taxes"
                label="Taxes"
                required
                item-text="name"
                item-value="id"
            ></v-select>
            <v-btn class="mr-4" @click="submit">submit</v-btn>
            <v-btn @click="clear">clear</v-btn>
        </v-form>
    </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Object || undefined
    },
    data() {
        return {
            valid: true,
            taxes: [],
            defaultValues: {},
            formFields: {
                id: 0,
                name: null,
                tax_id: null,
                taxable: false,
                is_default: false,
                created_by: null
            }
        };
    },
    mounted() {
        this.getAll({
            model: "taxes"
        }).then(taxes => {
            this.taxes = taxes;
        });
        this.defaultValues = this.formFields;
        if (this.$props.model) {
            this.formFields = {
                ...this.$props.model
            };
        }
    },
    methods: {
        submit() {
            let payload = {
                model: "stores",
                data: { ...this.formFields }
            };
            this.create(payload).then(() => {
                this.clear();
                this.$emit("submit", "stores");
            });
        },
        beforeDestroy() {
            this.$off("submit");
        },
        clear() {
            this.formFields = { ...this.defaultValues };
        },
        getAllTaxes() {
            this.getAll({
                model: "taxes"
            }).then(taxes => {
                this.taxes = taxes;
            });
        },
        ...mapActions({
            getAll: "getAll",
            getOne: "getOne",
            create: "create",
            delete: "delete"
        })
    },
    computed: {
        user_id: {
            get() {
                return this.$store.state.user.id;
            }
        }
    }
};
</script>
