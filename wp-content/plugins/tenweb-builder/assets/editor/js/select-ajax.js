jQuery(window).on('elementor:init', function () {
    var selectAjax = elementor.modules.controls.Select2.extend({

        isFirstTime: true,

        getSelect2DefaultOptions: function () {
            var _this = this;

            return jQuery.extend(elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply(this, arguments), {
                ajax: {
                    transport: function (params, success, failure) {

						var data = {
                            q: params.data.q,
                            filter_by: _this.model.get('filter_by'),
                            action: 'twbb_editor_get_posts'
                        };

                        var args = ['twbb_editor_select_ajax_get_options', {
                            data: data,
                            success: success,
                            error: failure
                        }];

                        return elementor.ajax.send.apply(elementor.ajax, args);
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 1
            });
        },
        setSavedOptions: function () {
            var _this = this
            var ids = this.getControlValue();
            var filterBy = this.model.get('filter_by');

            if ( !ids || !filterBy ) {
                return;
            }

            if ( !Array.isArray(ids) ) {
                ids = [ids];
            }
            elementor.ajax.loadObjects({
                action: 'twbb_editor_select_ajax_get_saved_options',
                ids: ids,
                data: {
                    filter_by: filterBy,
                    unique_id: '' + _this.cid + filterBy
                },
                before: function () {
                    _this.disableControl();
                },
                success: function (data) {

                    _this.isFirstTime = false;

                    _this.model.set('options', data);

                    _this.render();
                }
            });
        },

        disableControl: function () {
            this.ui.select.prop('disabled', true);
            this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
        },

        applySavedValue: function () {
            setTimeout(elementor.modules.controls.Select2.prototype.applySavedValue.bind(this));
            if (this.isFirstTime) {
                this.setSavedOptions();
            }
        }
    });

    elementor.addControlView('TWBBSelectAjax', selectAjax);
});