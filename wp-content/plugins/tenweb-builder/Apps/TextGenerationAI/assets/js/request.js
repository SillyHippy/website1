class RestRequest {
    constructor(route, params, method, success_callback, fail_callback, error_callback) {
        this.success_callback = success_callback;
        this.fail_callback = fail_callback;
        this.error_callback = error_callback;
        this.route = route;
        this.params = params;
        this.method = method;
        this.front_ai = false;
    }

    twbb_send_rest_request( front_ai, action_type ) {
        this.front_ai = front_ai;
        if ( twbb_write_with_ai_data.limitation_expired == "1" && action_type == 'builder' ) {
            this.show_error('plan_limit_exceeded');
            this.fail_callback({'data': 'plan_limit_exceeded'});
            return;
        } else if( twbb_write_with_ai_data.limitation_expired == "1" && action_type == 'builder_image' ) {

        } else if( twbb_write_with_ai_data.limitation_expired == "1" && action_type == 'sections' ) {

        }

        this.twbb_rest_request(this.route, this.params, this.method, function (that) {
            if(action_type != 'builder_image' && action_type != 'sections'){
                that.handle_ai_response(that.data, action_type);
            } else {
                that.get_ai_data(action_type);
            }
        });
    }

    twbb_rest_request(route, params, method, callback) {
        let rest_route = twbb_write_with_ai_data.rest_route + "/" + route;
        let form_data = null;
        if (params) {
            form_data = new FormData();
            for (let param_name in params) {
                form_data.append(param_name, params[param_name]);
            }
        }

        fetch(rest_route, {
            method: method,
            headers: {
                'X-WP-Nonce': twbb_write_with_ai_data.ajaxnonce
            },
            body: form_data,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data['success']) {
                    this.data = data;
                    callback(this);
                } else {
                    this.fail_result(data);
                }
            }).catch((error) => {
            this.error_callback(error);
        });
    }

    fail_result(err) {
        this.show_error(err.data);
        this.fail_callback(err);
    }

    get_ai_data( action_type ) {
        let self = this;
        setTimeout(function () {
            self.twbb_rest_request('ai_output', {'action_type' : action_type}, "POST", function (success) {
                success = success.data;

                if (success['data']['status'] !== 'done') {
                    self.get_ai_data(action_type);
                } else {
                    if (!success['data']['output'] && action_type == 'builder' ) {
                        this.show_error("something_wrong");
                        self.fail_callback(success);
                    }
                    else if( !success['data']['output'] && ( action_type == 'builder_image' || action_type == 'sections' ) ) {
                        self.fail_callback(success);
                    }
                    else
                    {
                        self.success_callback(success);
                    }
                }
            })
        }, 1000);
    }

    show_error( notif_key ) {
      if( notif_key == 'plan_limit_exceeded' ) {
        if (twbb_write_with_ai_data.plan == 'Free') {
            notif_key = 'free_limit_reached';
        } else {
            notif_key = 'plan_limit_reached';
        }
      }
      if (typeof twbb_write_with_ai_data.error_data[notif_key] === "undefined") {
         notif_key = "something_wrong";
      }

      let message = twbb_write_with_ai_data.error_data[notif_key]['text'];
      if ( this.front_ai ) {
          let iframe = jQuery("#elementor-preview-iframe").contents();
          if( iframe.find(".twbb-ai-front.twbb-ai-front-open .twbb-ai-front-new_prompt-loading").length) {
              iframe.find(".twbb-ai-front.twbb-ai-front-open .twbb-ai-front-new_prompt-textarea").after("<span class='ai-front-error'>" + message + "</span>");
          } else {
              iframe.find(".twbb-ai-front.twbb-ai-front-open .twbb-ai-front-loading").after("<span class='ai-front-error'>" + message + "</span>");
          }
      } else {
          jQuery(document).find(".twbb-ai-error-message").text(message).show();
      }

    }

    handle_ai_response( success, action_type ) {
        if (!success['data']['output'] && action_type == 'builder' ) {
            this.show_error("something_wrong");
            this.fail_callback(success);
        }
        else if( !success['data']['output'] && ( action_type == 'builder_image' || action_type == 'sections' ) ) {
            this.fail_callback(success);
        }
        else {
            this.success_callback(success);
        }
    }
}

function restRequestInstance(route, params, method, success_callback, fail_callback, error_callback){
    return new RestRequest(route, params, method, success_callback, fail_callback, error_callback);
}
