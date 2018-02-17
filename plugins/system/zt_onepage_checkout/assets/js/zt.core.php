<?php
/* Load Joomla! Framework */
define('_JEXEC', 1);
define('ZTVERSION', '0.0.0');
define('JPATH_BASE', realpath('../../../../../'));
require_once ( JPATH_BASE . '/includes/defines.php' );
require_once ( JPATH_BASE . '/includes/framework.php' );

/* Create the Application */
$app = JFactory::getApplication('site');
/* Load Crex */

$urlRoot = rtrim(str_replace('/plugins/system/zt_onepage_checkout/assets/js','',JUri::root()),'/');

header("Content-type: application/x-javascript; charset: UTF-8");
?>
/**     
 * @param {object} w Window pointer
 * @param {object} $ jQuery pointer
 * @returns {undefined}
 */
(function (w, $) {

    if (typeof w.zt === 'undefined') {
        /* Local zo2 definition */
        var _zt = {
            /* Common settings */
            settings: {
                /* Zt current version */
                version: "<?php echo ZTVERSION; ?>",
                /* Joomla! URL root */
                frontendUrl: "<?php echo $urlRoot . '/'; ?>",
                backendUrl: "<?php echo $urlRoot . '/administrator/'; ?>",
                /* Joomla! security torken */
                token: "<?php echo JSession::getFormToken(); ?>"
            },
            /* Internal jQuery/Zepto framework */
            $: $
        };
        /* Provide global zt object */
        w.zt = _zt;
    }

})(window, jQuery);
