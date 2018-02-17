/* Here you can include your additional Javascript code */
var _body = jQuery('body');

jQuery(document).ready(function (e) {
  sticky();
  line();
jQuery('.novis').parent().css('display','none');
jQuery('.novis').css('display','none');

jQuery('#shipment_id_2').on('click', function(){
  jQuery('#address_type_name_field').removeClass('required');
  jQuery('.address_type_name_field_lbl .asterisk').html('');
})

jQuery('#shipment_id_1').on('click', function(){
  jQuery('#address_type_name_field').addClass('required');
  jQuery('.address_type_name_field_lbl .asterisk').html('*');
})

})
jQuery(window).resize(function(){
  sticky();
  line();
});
jQuery(window).scroll(function(){
  sticky();
})
/* Sticky logo in home barber*/
function sticky() {
  var ww = jQuery(window).width();
  if(jQuery('body').hasClass('home-barber')) {
    var _menu = jQuery('.mobile-menu');
    var _header = jQuery('#zo2-header-wrap');
    var _book = jQuery('.btn-book-area');
    var _logo = jQuery('#sticky-logo');
    if(jQuery(window).scrollTop() >= 217) {
      if(ww > 991) _logo.prependTo('.mobile-menu');
      _header.addClass('sticky');
      _menu.addClass('up');
      _book.addClass('up');
    } else {
      if(ww > 991) _logo.appendTo('#zo2-header-logo');
      _header.removeClass('sticky');
      _menu.removeClass('up');
      _book.removeClass('up');
    }
    if(ww < 992) _logo.appendTo('#zo2-header-logo');
  }
}
/* Two lines in home furniture */
function line() {
 var ww = jQuery(window) .width();
 var wi = jQuery('.intro-block').width();
 var wl = ((ww - wi) / 2);
 var d = wl-15;
 var _block = jQuery('.intro-block');
 if(ww > 991) {
   _block.find('.block .line').width(wl-100);
   _block.find('.block:first-child .line').css('right','-'+d+'px');
   _block.find('.block:last-child .line').css('left','-'+d+'px');
 }
}
/* Grid view layout in shop */
jQuery('#windy-chose-view-style .page-view-item').on('click',function(e){
  var _shop = jQuery('#windy-show-shop');
  var _span = jQuery('#windy-chose-view-style .page-view-item');
  if(jQuery(this).attr('data-layout') == 'list-layout') {
    _span.removeClass('active-mode-preview');
    jQuery(this).addClass('active-mode-preview');
    _shop.addClass('list-layout').removeClass('grid-layout');
  } else if (jQuery(this).attr('data-layout') == 'grid-layout') {
    _span.removeClass('active-mode-preview');
    jQuery(this).addClass('active-mode-preview');
    _shop.addClass('grid-layout').removeClass('list-layout');
  }
})
if(jQuery('body').hasClass('cart')) {
  jQuery('.main-content').removeClass('col-md-9').addClass('col-md-12');
}
/* Toggle siderbar in home sidebar */
jQuery('#canvas-sidebar').on('click', function(){
  jQuery('body.home-sidebar').toggleClass('opensidebar');
})
jQuery('body.home-sidebar').on('click', '.zo2-wrapper', function(e){
  if (!jQuery(e.target).is('#canvas-sidebar') && _body.hasClass('opensidebar') && !jQuery(e.target).is('#zo2-sidebar-wrap') && !jQuery(e.target).parents().is('#zo2-sidebar-wrap')) {
    _body.removeClass('opensidebar');
  }
})

jQuery('.dropdown-toggle').on('click', '.caret', function(e){
  jQuery(this).closest('li').toggleClass('collapsed');
  return false;
})
jQuery('.mega-group').on('click', '.group-title', function(e){
  jQuery(this).closest('li').toggleClass('collapsed');
})

 jQuery(document).ready(function($) {
		$('a.ask-a-question, a.printModal, a.recommened-to-friend, a.manuModal').click(function(event){
		  event.preventDefault();
		  $.fancybox({
			href: $(this).attr('href'),
			type: 'iframe',
			height: 550
			});
		  });
		
	});