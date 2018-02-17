/**
 * Zt Ajax
 * @param {type} w
 * @param {type} z
 * @param {type} $
 * @returns {undefined}
 */
(function (w, z, $) {
    /* Reject if zt is not defined */
    if (typeof (z) === 'undefined') {
        console.log('Error: Zt Javsacript Framework not available.');
        return false;
    }
    /* Reject if Zt UI is not defined */
    if (typeof (z.ui) === 'undefined') {
        console.log('Error: Zt Javsacript Framework not available.');
        return false;
    }

    /* Local ajax class */
    var _ajax = {
        /* Local settings */
        _settings: {},
        ajaxDutyTimeout: 0,
        ajaxIsOnDuty: false,
        ajaxOverlay: false,
        /**
         * Init function
         * @returns {undefined}
         */
        _init: function () {
            this._settings = {
                url: z.settings.frontendUrl,
                type: "POST",
                data: {
                },
                beforeSend: function () {
                    if(z.ajax.ajaxOverlay){
                        z.ui.showAjaxOverlay();
                    }
                    z.ajax.ajaxIsOnDuty = true;
                },
                success: function (data) {
                    if (z.ajax.ajaxDutyTimeout) {
                        w.clearTimeout(z.ajax.ajaxDutyTimeout);
                        z.ajax.ajaxDutyTimeout = 0;
                    }
                    z.ajax.ajaxDutyTimeout = w.setTimeout(function () {
                        z.ui.hideAjaxOverlay();
                        z.ajax.ajaxIsOnDuty = false;
                    }, 1000);
                    $.each(data, function (index, item) {
                        switch (item.type) {
                            case 'html':
                                z.ui.replace(item.data.target, item.data.html);
                                break;
                            case 'appendHtml':
                                z.ui.append(item.data.target, item.data.html);
                                break;
                            case 'exec':
                            case 'execute':
                                eval(item.data.toString());
                                break;
                            case 'message':
                               // z.ui.raiseMessage(item.data.message);
                                break;
                            default:
                                break;
                        }
                        ;
                    });
                },
                error: function () {
                    if (z.ajax.ajaxDutyTimeout) {
                        w.clearTimeout(z.ajax.ajaxDutyTimeout);
                        z.ajax.ajaxDutyTimeout = 0;
                    }
                    z.ajax.ajaxDutyTimeout = w.setTimeout(function () {
                        z.ui.hideAjaxOverlay();
                        z.ajax.ajaxIsOnDuty = false;
                    }, 1000);
                }
            };
            this._settings.data[z.settings.token] = 1;
        },
        /**
         * Ajax request manually
         * @param {type} data
         * @returns {jqXHR}
         */
        request: function (data, ajaxOverlay) {
            var buffer = {};
            $.extend(true, buffer, this._settings);
            $.extend(true, buffer, (typeof (data) === 'undefined') ? {} : data);
            z.ajax.ajaxOverlay = (typeof(ajaxOverlay) === 'undefined') ? false : ajaxOverlay;
            return $.ajax(buffer);
        },
        /**
         * Ajax request by form data
         * @param {type} formSelector
         * @param {type} data
         * @param {type} getArray
         * @returns {jqXHR}
         */
        formRequest: function (formSelector, data, getArray, ajaxOverlay) {
            var $form = $(formSelector);
            var data = (typeof (data) === 'undefined') ? {} : data;
            var getArray = (typeof (getArray) === 'undefined') ? false : getArray;
            var formData = {};
            var arrayDetect = {};
            var arrayValue = {};
            if ($form.length > 0) {
                var $inputs = $form.find("input, texarea, select, button");
                $inputs.each(function () {
                    var $me = $(this);
                    var type = $me.attr('type');
                    var value = $me.val();
                    var name = $me.attr('name');
                    if (typeof (name) !== 'undefined') {
                        if (typeof (type) !== 'undefined') {
                            if (type === 'checkbox' || type === 'radio') {
                                /* Convert to boolean value if checkbox/radio value is empty */
                                if (value === '') {
                                    value = ($me.is(':checked')) ? true : false;
                                }
                            }
                        }
                        //Filter radio
                        if(type !== 'radio'){
                            formData[name] = value;
                        }else{
                            if($me.is(':checked')){
                                formData[name] = value;
                            }                            
                        }                        
                        if (getArray && type !== 'radio') {
                            arrayDetect[name] = (arrayDetect.hasOwnProperty(name)) ? arrayDetect[name] + 1 : 1;
                            if (!arrayValue.hasOwnProperty(name)) {
                                arrayValue[name] = [];
                            }
                            arrayValue[name].push(value);
                        }
                    }
                });
                if (getArray) {
                    /* If many fields has same name convert it to an array */
                    $.each(arrayDetect, function (index, value) {
                        if (value > 1) {
                            formData[index] = arrayValue[index];
                        }
                    });
                }
            }
            var buffer = {};
            $.extend(true, buffer, {data: formData});
            $.extend(true, buffer, data);
            return this.request(buffer, ajaxOverlay);
        },
        /**
         * Un hook
         * @param {type} selector
         * @returns {undefined}
         */
        unHook: function (selector) {
            $(selector).off('submit');
        },
        /**
         * Check form is valid
         * @param {type} selector
         * @returns {undefined}
         */
        formIsValid: function checkFormValidation(selector) {
            var checkValid = typeof ($.fn.isValid) !== 'undefined';
            var $current = $(selector);
            if (checkValid) {
                if ($current.isValid()) {
                    return true;
                } else {
                    if(typeof($current.data('validation-error')) !== 'undefined'){
                        z.ui.rasieTextMessage('warning', $current.data('validation-error'));
                    }
                    return false;
                }
            } else {
                return true;
            }
        },
        /**
         * Form hook
         * @param {type} selector
         * @param {type} data
         * @param {type} getArray
         * @param {type} callback
         * @returns {undefined}
         */
        formHook: function (selector, data, getArray, callback, ajaxOverlay) {
            var self = this;
            if ($(selector).length <= 0) {
                return false;
            }
            var data = (typeof (data) === 'undefined') ? {} : data;
            var getArray = (typeof (getArray) === 'undefined') ? false : getArray;
            var callback = (typeof (callback) === 'undefined') ? function () {
            } : callback;
            $(selector).on('submit', function () {
                if (typeof ($(this).data('nosubmit')) !== 'undefined') {
                    if ($(this).data('nosubmit') === true){
                        return false;
                    }
                }
                if (self.formIsValid(selector)) {
                    self.formRequest(this, data, getArray, ajaxOverlay).done(function () {
                        callback();
                    });
                }
                return false;
            });
        }
    };

    /* Append to Zt JS Framework */
    z.ajax = _ajax;
    z.ajax._init();

})(window, zt, zt.$);
