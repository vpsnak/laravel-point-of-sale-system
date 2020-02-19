const actions = {
  post(context, payload) {
    return new Promise((resolve, reject) => {
      const successNotification = payload.success_notification || false;
      const errorNotification = payload.error_notification;
      const mutations = payload.mutations;
      const console_out = process.env.NODE_ENV === "development" ? true : false;

      axios
        .post(
          `${context.rootState.config.base_url}/${payload.endpoint}`,
          payload.data
        )
        .then(response => {
          if (successNotification && _.has(response.data, "notification")) {
            context.commit("setNotification", response.data.notification);
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

          if (errorNotification && _.has(error_response, "errors")) {
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
