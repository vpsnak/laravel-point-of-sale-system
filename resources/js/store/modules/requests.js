const actions = {
  request(context, payload) {
    return new Promise((resolve, reject) => {
      const noSuccessNotification = payload.no_success_notification;
      const noErrorNotification = payload.no_error_notification;
      const mutations = payload.mutations;
      const console_out = process.env.NODE_ENV === "development" ? true : false;

      axios({
        method: payload.method,
        url: `${context.rootState.config.base_url}/${payload.endpoint}`,
        data: payload.data
      })
        .then(response => {
          if (!noSuccessNotification && _.has(response.data, "notification")) {
            context.commit("setNotification", response.data.notification, {
              root: true
            });
          }

          if (mutations) {
            mutationss.forEach(mutation => {
              context.commit(mutation, response.data);
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
          }
          if (console_out) {
            console.error(error_response);
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
