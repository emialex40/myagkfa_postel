/**
 * Zt Joomla
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

    /* Local ajax class */
    var _joomla = {
        /* Local settings */
        _settings: {},
        /**
         * Init function
         * @returns {undefined}
         */
        _init: function () {
        },
        /**
         * Update joomla torken
         * @param {type} token
         * @returns {undefined}
         */
        updateToken: function (token) {
            /* Delete old torken */
            $.each(z.ajax._settings.data, function(index, item){
                var key = index.toString();
                if(key.length === 32 && item === 1){
                    delete z.ajax._settings.data[key];
                }
            });
            z.settings.token = token;
            z.ajax._settings.data[token] = 1;
        }
    };

    /* Append to Zt JS Framework */
    z.joomla = _joomla;
    z.joomla._init();

})(window, zt, zt.$);
