import router from "../../plugins/router";

const actions = {
  request(context, payload) {
    return new Promise((resolve, reject) => {
      let headers = {
        Authorization: context.rootState.token
      };
      if (payload.data instanceof FormData) {
        options.headers["Content-Type"] = "multipart/form-data";
      }
      const noSuccessNotification = payload.no_success_notification;
      const noErrorNotification = payload.no_error_notification;
      const console_out = process.env.NODE_ENV === "development" ? true : false;

      if (console_out) {
        console.info({
          request: `${payload.method} request to: ${context.rootState.config.base_url}/${payload.url}`,
          payload: payload
        });
      }

      axios({
        method: payload.method,
        url: `${context.rootState.config.base_url}/${payload.url}`,
        data: payload.data,
        headers
      })
        .then(response => {
          if (!noSuccessNotification && _.has(response.data, "notification")) {
            context.commit("setNotification", response.data.notification, {
              root: true
            });
          }

          if (console_out) {
            console.info(response.data);
          }

          resolve(response.data);
        })
        .catch(error => {
          let error_response;

          if (_.has(error, "response")) {
            if (_.has(error.response, "data")) {
              error_response = error.response.data;
            } else {
              error_response = error.response;
            }
          } else {
            error_response = error;
          }

          if (!noErrorNotification && _.has(error_response, "errors")) {
            context.commit(
              "setNotification",
              {
                msg: error_response.errors,
                type: "error"
              },
              { root: true }
            );
          } else if (
            !noErrorNotification &&
            _.has(error_response, "message") &&
            _.has(error_response, "exception")
          ) {
            context.commit(
              "setNotification",
              {
                msg: [
                  "Unhandled Exception",
                  `Message: ${error_response.message}<br>${error_response.file}<br>line: ${error_response.line}`
                ],
                type: "error"
              },
              { root: true }
            );
          }
          if (console_out) {
            console.error(error_response);
          }

          if (
            error.response.status === 401 &&
            router.currentRoute.name !== "login"
          ) {
            context.dispatch("logout", null, { root: true });
          }

          reject(error_response);
        });
    });
  }
};

export default {
  namespaced: true,
  actions
};
