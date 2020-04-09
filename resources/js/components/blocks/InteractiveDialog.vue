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
        <v-icon v-if="dialog.icon" class="pr-2" v-text="dialog.icon" />

        <span v-text="dialog.title" />

        <v-spacer v-if="dialog.titleCloseBtn" />

        <v-tooltip v-if="dialog.titleCloseBtn" bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              @click.stop="closeEvent(false, true)"
              color="red"
              icon
              v-on="on"
            >
              <v-icon v-text="'mdi-close'" />
            </v-btn>
          </template>
          <span v-text="'Close'" />
        </v-tooltip>
      </v-card-title>

      <v-divider class="mb-3" />

      <v-card-text v-if="!dialog.no_padding">
        <component
          v-if="dialog.component"
          :is="dialog.component"
          v-bind="dialog.component_props"
          :readonly="dialog.readonly"
          @submit="submit"
        />
        <div
          v-else
          v-html="dialog.content"
          :class="dialog.contentClass || ''"
        />
      </v-card-text>

      <div v-else>
        <component
          v-if="dialog.component"
          :is="dialog.component"
          v-bind="dialog.component_props"
          :readonly="dialog.readonly"
          @submit="submit"
        />
        <div
          v-else
          v-html="dialog.content"
          :class="dialog.contentClass || ''"
        />
      </div>
      <!-- confirmation actions -->
      <v-card-actions
        v-if="dialog.action === 'confirmation' || dialog.action === 'info'"
        class="align-center mt-5"
      >
        <v-spacer />

        <v-btn
          v-if="dialog.action === 'confirmation'"
          @click="closeEvent(false, true)"
          text
          color="error"
        >
          {{ dialog.cancelBtnTxt }}
        </v-btn>

        <v-btn @click="closeEvent(true, true)" text outlined color="primary">
          {{ dialog.confirmationBtnTxt }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { EventBus } from "../../plugins/eventBus";
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

  mounted() {
    console.log(this.dialog.component_props);
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
