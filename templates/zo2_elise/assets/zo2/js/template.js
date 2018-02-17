jQuery(document).ready(function(){  
    var _search = jQuery('.search-area');
    _search.on('click', '.search-open', function(e){
        _search.find('.search-form').addClass('open');
    })
    jQuery('.search-form').on('click', function(e) {
        if(!jQuery(e.target).is('form') && !jQuery(e.target).is('input')) {
            jQuery('.search-form').removeClass('open');
        }
    })
    var _wrap = jQuery('.zo2-wrapper');
    jQuery('.btn-wishlist').on('click', function(e) {
        _wrap.toggleClass('is-shopbag');
        jQuery('.zt-cart-bar').toggleClass('open');
        return false;
    });
    _wrap.on('click', function(e){
        if(jQuery(this).hasClass('is-shopbag')) {
            if(!jQuery(e.target).parents().hasClass('zt-cart-bar') && !jQuery(e.target).hasClass('zt-cart-bar')) {
                _wrap.removeClass('is-shopbag');
                jQuery('.zt-cart-bar').removeClass('open');
            }
        }
    })
})