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
        <v-icon v-if="dialog.icon" class="pr-2">{{ dialog.icon }}</v-icon>
        {{ dialog.title }}
        <v-spacer v-if="dialog.titleCloseBtn"></v-spacer>
        <v-btn
          v-if="dialog.titleCloseBtn"
          @click.stop="closeEvent(false, true)"
          icon
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
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
import { mapActions, mapMutations, mapState } from "vuex";

export default {
  beforeDestroy() {
    EventBus.$off();
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
      if (!payload) {
        this.closeEvent(true);
      } else {
        if (payload.notification) {
          this.setNotification({
            msg: payload.notification.msg,
            type: payload.notification.type
          });
        }

        if (payload.data) {
          this.closeEvent(payload.data, true);
        } else {
          this.closeEvent(true, true);
        }
      }
    },
    closeEvent(payload, close) {
      if (!this.dialog.persistent || close) {
        if (event.isTrusted && this.dialog.eventChannel) {
          let a = EventBus.$emit(this.dialog.eventChannel, {
            payload
          });
          console.log(a);

          console.log({
            module: "interactive-dialog",
            event,
            payload: payload,
            event_channel: this.dialog.eventChannel
          });
        }

        this.resetDialog();
      }
    }
  }
};
</script>
