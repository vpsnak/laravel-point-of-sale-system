<template>
  <v-dialog
    v-model="dialog.show"
    :persistent="dialog.persistent"
    :max-width="dialog.width"
    :fullscreen="dialog.fullscreen"
    scrollable
    @click:outside.stop="closeEvent(false)"
  >
    <v-card>
      <v-card-title>
        <v-icon v-if="dialog.icon" class="pr-2">
          {{ dialog.icon }}
        </v-icon>

        {{ dialog.title }}

        <v-spacer v-if="dialog.titleCloseBtn" />

        <v-tooltip v-if="dialog.titleCloseBtn" bottom color="red">
          <template v-slot:activator="{ on }">
            <v-btn
              @click.stop="closeEvent(false, true)"
              color="red"
              icon
              v-on="on"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
          <span>Close</span>
        </v-tooltip>
      </v-card-title>

      <v-divider class="mb-3"></v-divider>

      <v-card-text>
        <component
          v-if="dialog.component"
          :is="dialog.component"
          :readonly="dialog.readonly"
          :model="dialog.model"
          @submit="submit"
        ></component>
        <div
          v-else
          v-html="dialog.content"
          :class="dialog.contentClass || ''"
        ></div>
      </v-card-text>

      <!-- confirmation actions -->
      <v-card-actions
        class="d-flex align-center mt-5"
        v-if="dialog.action === 'confirmation' || dialog.action === 'info'"
      >
        <div class="flex-grow-1"></div>

        <v-btn
          v-if="dialog.action === 'confirmation'"
          @click="closeEvent(false, true)"
          text
          color="error"
          >{{ dialog.cancelBtnTxt }}</v-btn
        >

        <v-btn @click="closeEvent(true, true)" text color="success">{{
          dialog.confirmationBtnTxt
        }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { EventBus } from "../../plugins/event-bus";
import { mapMutations, mapState } from "vuex";

export default {
  beforeDestroy() {
    if (this.dialog.eventChannel) {
      EventBus.$off(this.dialog.eventChannel);
    }
  },

  computed: {
    ...mapState("dialog", ["interactive_dialog"]),

    dialog() {
      return this.interactive_dialog;
    }
  },

  methods: {
    ...mapMutations(["setNotification"]),
    ...mapMutations("dialog", ["resetDialog"]),

    submit(payload) {
      if (_.has(payload, "notification")) {
        this.setNotification({
          msg: payload.notification.msg,
          type: payload.notification.type
        });
      }

      if (_.has(payload, "data")) {
        this.closeEvent(payload.data, true);
      } else {
        this.closeEvent(payload, true);
      }
    },
    closeEvent(payload, close) {
      if (!this.dialog.persistent || close) {
        if (this.dialog.eventChannel) {
          EventBus.$emit(this.dialog.eventChannel, {
            payload
          });

          console.info({
            module: "interactive-dialog",
            payload,
            event_channel: this.dialog.eventChannel
          });
        }

        this.resetDialog();
      }
    }
  }
};
</script>
