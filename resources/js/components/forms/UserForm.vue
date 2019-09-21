<template>
  <v-form v-model="valid">
    <div class="text-center">
      <v-chip color="indigo darken-4" label>
        <v-icon left>fas fa-user-circle</v-icon>User Form
      </v-chip>
    </div>
    <v-text-field v-model="name" :rules="nameRules" :counter="10" label="Name" required></v-text-field>
    <v-text-field v-model="email" :rules="emailRules" label="E-mail" required></v-text-field>
    <v-text-field
      v-model="password"
      :append-icon="show ? 'visibility' : 'visibility_off'"
      :rules="[passwordRules.required, passwordRules.min]"
      :type="show ? 'text' : 'password'"
      name="input-10-1"
      label="Password"
      hint="At least 8 characters"
      counter
      @click:append="show = !show"
    ></v-text-field>
  </v-form>
</template>

<script>
export default {
  data: () => ({
    valid: false,
    name: "",
    nameRules: [
      v => !!v || "Name is required",
      v => v.length <= 10 || "Name must be less than 10 characters"
    ],
    email: "",
    emailRules: [
      v => !!v || "E-mail is required",
      v => /.+@.+/.test(v) || "E-mail must be valid"
    ],
    show: false,
    password: "Password",
    passwordRules: {
      required: value => !!value || "Required.",
      min: v => v.length >= 8 || "Min 8 characters"
    }
  })
};
</script>