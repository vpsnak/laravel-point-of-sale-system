<template>
    <div>
        <v-form>
            <div class="text-center">
                <v-chip color="primary" label>
                    <v-icon left>fas fa-gifts</v-icon>Gift Card Form
                </v-chip>
            </div>
            <v-text-field v-model="id" type="number" label="ID" required></v-text-field>
            <v-text-field v-model="name" label="Name" required></v-text-field>
            <v-text-field v-model="code" label="Code" required></v-text-field>
            <v-switch v-model="enabled" :label="`Enabled : ${enabled.toString()}`"></v-switch>
            <v-text-field v-model="amount" type="number" label="Amount" required></v-text-field>

            <v-btn class="mr-4" @click="submit">submit</v-btn>
            <v-btn @click="clear">clear</v-btn>
        </v-form>
        <v-alert
            v-if="savingSuccessful === true"
            class="mt-4"
            type="success"
        >Form submitted successfully!</v-alert>
    </div>
</template>
<script>
import { mapActions } from "vuex";

export default {
    data: () => ({
        savingSuccessful: false,
        id: null,
        name: null,
        code: null,
        enabled: false,
        amount: null
    }),
    methods: {
        submit() {
            let payload = {
                model: "gift-cards",
                data: {
                    id: this.id,
                    name: this.name,
                    code: this.code,
                    enabled: this.enabled,
                    amount: this.amount
                }
            };
            this.create(payload).then(() => {
                this.clear();
                this.savingSuccessful = true;
                window.location.reload();
            });
        },
        clear() {
            this.id = null;
            this.name = null;
            this.code = null;
            this.enabled = null;
            this.amount = null;
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
