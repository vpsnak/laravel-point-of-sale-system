const state = {
  interactive_dialog: {
    show: false,
    width: 450,
    fullscreen: false,
    icon: "",
    title: "",
    titleCloseBtn: false,
    component: "",
    component_props: {},
    readonly: false,
    content: "",
    persistent: false,
    action: "",
    cancelBtnTxt: "Cancel",
    confirmationBtnTxt: "OK",
    content: "",
    contentClass: "",
    eventChannel: "",
    no_padding: false
  }
};

// mutations
const mutations = {
  setDialog(state, value) {
    state.interactive_dialog = { ...state.interactive_dialog, ...value };
  },
  resetDialog(state) {
    state.interactive_dialog.show = false;
    state.interactive_dialog.width = 450;
    state.interactive_dialog.fullscreen = false;
    state.interactive_dialog.icon = "";
    state.interactive_dialog.title = "";
    state.interactive_dialog.titleCloseBtn = false;
    state.interactive_dialog.component = "";
    state.interactive_dialog.component_props = {};
    state.interactive_dialog.readonly = false;
    state.interactive_dialog.content = "";
    state.interactive_dialog.persistent = false;
    state.interactive_dialog.action = "";
    state.interactive_dialog.cancelBtnTxt = "Cancel";
    state.interactive_dialog.confirmationBtnTxt = "OK";
    state.interactive_dialog.content = "";
    state.interactive_dialog.contentClass = "";
    state.interactive_dialog.eventChannel = "";
    state.interactive_dialog.no_padding = false;
  }
};

export default {
  namespaced: true,
  state,
  mutations
};
