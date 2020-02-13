<template>
    <v-container>
        <v-row dense>
            <v-col :cols="12" :lg="6" :offset-lg="3" class="pa-2">
                <v-textarea
                    label="Notes"
                    prepend-icon="mdi-note-text"
                    v-model="delivery.notes"
                    counter
                    clearable
                    no-resize
                    outlined
                ></v-textarea>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
    computed: {
        ...mapState("cart", ["delivery"]),

        notes: {
            get() {
                return this.delivery.notes;
            },
            set(value) {
                this.setNotes(value);
            }
        }
    },
    methods: {
        ...mapMutations("cart", ["setNotes"])
    }
};
</script>
