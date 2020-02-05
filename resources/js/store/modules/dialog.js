const state = {
    interactive_dialog: {
        show: false,
        width: 600,
        fullscreen: false,
        icon: "",
        title: "",
        titleCloseBtn: false,
        component: "",
        content: "",
        model: null,
        persistent: false
    }
};

// mutations
const mutations = {
    editItem(state, item) {
        state.interactive_dialog.show = true;
        state.interactive_dialog.fullscreen = false;
        state.interactive_dialog.width = 600;
        state.interactive_dialog.title = `Edit item #${item.id}`;
        state.interactive_dialog.titleCloseBtn = true;
        state.interactive_dialog.icon = "mdi-pencil";
        state.interactive_dialog.component = item.form;
        state.interactive_dialog.model = _.cloneDeep(item);
        state.interactive_dialog.persistent = true;
    },

    viewItem(state, item) {
        state.interactive_dialog.show = true;
        state.interactive_dialog.fullscreen = false;
        state.interactive_dialog.width = 600;
        state.interactive_dialog.title = `View item #${item.id}`;
        state.interactive_dialog.titleCloseBtn = true;
        state.interactive_dialog.icon = "mdi-eye";
        state.interactive_dialog.component = item.form;
        state.interactive_dialog.model = item;
        state.interactive_dialog.persistent = false;
    },

    setDialog(state, value) {
        value.show ? (state.interactive_dialog.show = value.show) : "";
        value.width ? (state.interactive_dialog.width = value.width) : "";
        value.fullscreen
            ? (state.interactive_dialog.fullscreen = value.fullscreen)
            : "";
        value.icon ? (state.interactive_dialog.icon = value.icon) : "";
        value.title ? (state.interactive_dialog.title = value.title) : "";
        value.titleCloseBtn
            ? (state.interactive_dialog.titleCloseBtn = value.titleCloseBtn)
            : "";
        value.component
            ? (state.interactive_dialog.component = value.component)
            : "";
        value.content ? (state.interactive_dialog.content = value.content) : "";
        value.model ? (state.interactive_dialog.model = value.model) : "";
        value.persistent
            ? (state.interactive_dialog.persistent = value.persistent)
            : "";
    },
    resetDialog(state) {
        state.interactive_dialog.show = false;
        state.interactive_dialog.width = 600;
        state.interactive_dialog.fullscreen = false;
        state.interactive_dialog.icon = "";
        state.interactive_dialog.title = "";
        state.interactive_dialog.titleCloseBtn = false;
        state.interactive_dialog.component = "";
        state.interactive_dialog.content = "";
        state.interactive_dialog.model = null;
        state.interactive_dialog.persistent = false;
    }
};

export default {
    namespaced: true,
    state,
    mutations
};
