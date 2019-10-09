<template>
    <v-form>
        <div class="text-center">
            <v-chip color="primary" label>
                <v-icon left>fas fa-money-bill-wave</v-icon>Tax Form
            </v-chip>
        </div>
        <v-text-field v-model="formFields.name" counter label="Name" required></v-text-field>
        <v-text-field v-model="formFields.percentage" counter label="Percentage" required></v-text-field>
        <v-switch v-model="formFields.is_default" label="Default"></v-switch>
        <v-btn class="mr-4" @click="submit">submit</v-btn>
        <v-btn @click="clear">clear</v-btn>
    </v-form>
</template>

<script>
import { mapActions } from "vuex";

export default {
    props: {
        model: Object || undefined
    },
    data() {
        return {
            defaultValues: {},
            formFields: {
                name: "",
                percentage: "",
                is_default: false
            }
        };
    },
    mounted() {
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
                model: "taxes",
                data: { ...this.formFields }
            };
            this.create(payload).then(() => {
                this.clear();
                this.$emit("submit", "taxes");
            });
        },
        beforeDestroy() {
            this.$off("submit");
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
    }
};
</script>
