<template>
    <v-form v-model="valid">
        <div class="text-center">
            <v-chip color="indigo darken-4" label>
                <v-icon left>fas fa-user-circle</v-icon>User Form
            </v-chip>
        </div>
        <v-text-field v-model="formFields.name" :counter="10" label="Name" required></v-text-field>
        <v-text-field v-model="formFields.email" :rules="emailRules" label="E-mail" required></v-text-field>
        <v-text-field
            v-model="formFields.password"
            :append-icon="show ? 'visibility' : 'visibility_off'"
            :rules="[passwordRules.required]"
            :type="show ? 'text' : 'password'"
            name="input-10-1"
            label="Password"
            hint="At least 8 characters"
            counter
            @click:append="show = !show"
        ></v-text-field>
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
            valid: false,
            show: false,
            defaultValues: {},
            formFields: {
                id: 0,
                name: null,
                email: null,
                password: null
            },
            emailRules: [
                // v => !!v || "E-mail is required",
                v => /.+@.+/.test(v) || "E-mail must be valid"
            ],
            passwordRules: {
                required: value => !!value || "Required."
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
                model: "users",
                data: { ...this.formFields }
            };
            this.create(payload).then(() => {
                this.clear();
                this.$emit("submit", "users");
            });
        },
        beforeDestroy() {
            this.$off("submit");
        },
        clear() {
            this.formFields = { ...this.defaultValues };
        },
        ...mapActions({
            create: "create"
        })
    }
};
</script>