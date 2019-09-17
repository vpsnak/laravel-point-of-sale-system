<template>
  <v-form v-model="valid">
		<v-row justify="center">     
             <!-- $table->bigIncrements('id');
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->string('photo_url');
            $table->softDeletes();
            $table->timestamps();
            // stock_id price_id foreign
        }); -->
        <v-col
          cols="12"
        >
          <div class="text-center">
            <v-chip
      color="primary"
      label
    >
      <v-icon left>fab fa-java</v-icon>
     Product Form 
    </v-chip>
    </div>
            </v-col>
        <v-col
          cols="3"
        >
          <v-text-field
            v-model="sku"
            :rules="nameRules"
            :counter="10"
            label="Sku"
            required
          ></v-text-field>
        </v-col>

        <v-col
          cols="3"
        >
          <v-text-field
            v-model="name"
            :rules="nameRules"
            :counter="10"
            label="Name"
            required
          ></v-text-field>
        </v-col>

        <v-col
          cols="3"
        >
          <v-text-field
            v-model="photo_url"
            label="Photo Url"
            required
          ></v-text-field>
        </v-col>
      </v-row>
  </v-form>
</template>

<script>
  export default {
    data: () => ({
      valid: false,
      firstname: '',
      lastname: '',
      nameRules: [
        v => !!v || 'Name is required',
        v => v.length <= 10 || 'Name must be less than 10 characters',
      ],
      email: '',
      emailRules: [
        v => !!v || 'E-mail is required',
        v => /.+@.+/.test(v) || 'E-mail must be valid',
      ],
    }),
  }
</script>