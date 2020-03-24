<template>
  <ValidationObserver v-slot="{ invalid }">
    <v-form @submit.prevent="submit">
      <v-container fluid class="overflow-y-auto" style="max-height: 60vh">
        <ValidationProvider v-slot="{ errors }" name="Role">
          <v-select
            hide-selected
            v-model="role_id"
            :items="roles"
            label="Role"
            required
            :loading="loading"
            :disabled="loading"
            :error-messages="errors"
            item-text="name"
            item-value="id"
          ></v-select>
        </ValidationProvider>
      </v-container>
      <v-container>
        <v-row>
          <v-col cols="12" align="center" justify="center">
            <v-btn
              color="primary"
              class="mr-4"
              type="submit"
              :disabled="invalid || loading"
              :loading="loading"
              >submit
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </ValidationObserver>
</template>
<script>
import { mapActions } from "vuex";

export default {
  props: {
    model: Object
  },
  mounted() {
    this.getAllUserRoles();
    this.user_name = this.$props.model.name;
    this.user_id = this.$props.model.id;
    this.role_id = this.$props.model.roles[0].id;
  },

  beforeDestroy() {
    this.$off("submit");
  },

  data() {
    return {
      loading: false,
      user_name: null,
      loading: false,
      roles: [],
      id: null,
      role_id: null
    };
  },

  methods: {
    ...mapActions(["setRole"]),
    ...mapActions("requests", ["request"]),

    submit() {
      this.loading = true;
      const payload = {
        method: "post",
        url: "roles/set",
        data: {
          user_id: this.user_id,
          role_id: this.role_id
        }
      };
      this.request(payload)
        .then(() => {
          this.$emit("submit", true);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    getAllUserRoles() {
      this.loading = true;
      this.request({
        method: "get",
        url: "roles"
      })
        .then(response => {
          this.roles = response;
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>
