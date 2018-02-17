/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 105 $
 * $LastChangedDate: 2017-01-23 14:03:40 +0530 (Mon, 23 Jan 2017) $
 * $Id: admin.js 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
(function($) {
	$(function() {
		var $title = $('.vp-extension-description .extension-title [data-text]');
		$title.text($title.data('text'));

		$('.hide-on-j3').closest('.control-group').hide();
		$('#vp-inline-stylesheet').remove();

		$('#jform_params_show_social_login[disabled], #jform_params_social_btn_size[disabled]').closest('.control-group').wrapAll('<div id="only-for-vpau" />');
		$('#only-for-vpau').append('<div id="only-for-vpau-overlay" />');

		$(window).load(function() {
			$('#only-for-vpau').width($('#only-for-vpau > .control-group:first-child').width());
		});

		var title;
		$('label').each(function() {
			title = $(this).attr('title');
			if (title && title.length && title.indexOf('</strong>') && title.indexOf('<br/>')) {
				title = title.replace('<br/>', '');
				if (title.indexOf('<br />') === -1) {
					title = title.replace('</strong>', '</strong><br/>');
				}
				$(this).attr('title', title);
			}
		});

		$('.hasTooltip').tooltip({
			'html': true,
			'container': 'body'
		});
	});
})(jQuery);
