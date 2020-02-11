<template>
  <v-tabs vertical>
    <v-tab>
      <v-icon left>mdi-cash-multiple</v-icon>
      <v-spacer />
      <span>Payments & Refunds</span>
    </v-tab>
    <v-tab>
      <v-icon left>mdi-package-variant</v-icon>
      <v-spacer />
      <span>Items</span>
    </v-tab>
    <v-tab>
      <v-icon left>mdi-file-document-edit-outline</v-icon>
      <v-spacer />
      <v-icon left>mdi-file-document-edit</v-icon>
      <span>Order Options</span>
    </v-tab>

    <v-tab-item>
      <v-card flat>
        <v-card-text>
          <paymentHistory></paymentHistory>
        </v-card-text>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <v-card-text>
          <p>
            Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas,
            lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed
            mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non
            adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt
            non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis.
          </p>
        </v-card-text>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <v-card-text>
          <p>
            Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam
            eget dui. Proin viverra, ligula sit amet ultrices semper, ligula
            arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna,
            posuere eget, vestibulum et, tempor auctor, justo.
          </p>
        </v-card-text>
      </v-card>
    </v-tab-item>
  </v-tabs>
</template>

<script>
export default {};
</script>
