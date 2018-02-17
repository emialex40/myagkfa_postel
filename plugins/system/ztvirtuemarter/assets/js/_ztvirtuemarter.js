(function (w, $) {
    if (typeof w.ZtVirtuemarter === 'undefined') {
        var _ZtVirtuemarter = {
            /* Internal jQuery */
            jQuery: $,

            _elements: {
                vm3Product: '.product .spacer, .product-item .spacer, .products .spacer'
            },

            /**
             * Init function
             * @returns {undefined}
             */
            _init: function () {
                if($( window ).width() < 768 ) {
                    $('body').addClass('zt-mobile');
                }
                var $iconDropdown = $('#mod_wishlists, #zt_top_cart, #mod_compare');
                $iconDropdown.find('.zt-cart-inner').hide();
                $iconDropdown.hover(
                    function () {
                        if(!$('body').hasClass('zt-mobile'))
                            $(this).find('.zt-cart-inner').delay(200).slideDown(500);
                    },
                    function () {
                        if(!$('body').hasClass('zt-mobile'))
                            $(this).find('.zt-cart-inner').slideUp(500);
                    }
                );
            },

            actionButtons: function (quickviewEnable, compareEnable, wishlistEnable) {
                var $this = this;
                $($this._elements.vm3Product).each(function () {
                    var priceId = $(this).find('.product-price').attr('id');
                    if(priceId) 
                        var productId = priceId.replace('productPrice', '');
                    var html = '<div class="zt-product-action">';
                    if (quickviewEnable == 1)
                        html += $this.buttonQuickView(productId);
                    if (compareEnable == 1)
                        html += $this.buttonCompare(productId);
                    if (wishlistEnable == 1)
                        html += $this.buttonWishlist(productId);
                    html += '</div>';
                    $(this).append(html);
                });
            },

            buttonWishlist: function (id) {
                return '<div class="wishlist wishlists'+id+'">'
                    + '<a class="wishlist-label add-wishlist hasTooltip " title="Отложить" onclick="ZtVirtuemarter.wishlist.add('+id+');">'
                    + '    <i class="fa fa-heart-o"></i>'
                    + '</a>'
                    + '</div>';
            },

            buttonCompare: function (id) {
                return '<div class="compare compare'+id+'">'
                    + '<a class="compare-label add-compare hasTooltip " title="Сравнить" onclick="ZtVirtuemarter.compare.add('+id+');">'
                    + '<i class="fa fa-files-o"></i>'
                    + '</a>'
                    + '</div>';
            },

            buttonQuickView: function (id) {
                return '<div class="quickview quickview'+id+'">'
                    + '<a class="quickview-label add-quickview hasTooltip " title="Посмотреть" onclick="ZtVirtuemarter.quickview.show('+id+');">'
                    + '<i class="fa fa-search"></i>'
                    + '</a>'
                    + '</div>';
            },

            countdown: function(enable) {
                if(enable != 1)
                    return false;

                var $this = this;
                var ids = [];
                $($this._elements.vm3Product).each(function (index) {
                    var priceId = $(this).find('.product-price').attr('id');
                    if(priceId)
                        ids[index] = priceId.replace('productPrice', '');
                });
                $.ajax({
                    url: 'index.php?action=countdown',
                    type: 'POST',
                    dataType: "json",
                    cache: false,
                    data: 'countdownProductIds=' + ids.join('-'),
                    success: function (data) {
                        if(typeof data == 'object') data = JSON.stringify(data);
                        var countdownData = JSON.parse(data);
                        $($this._elements.vm3Product).each(function () {
                            var priceId = $(this).find('.product-price').attr('id');
                            var id = priceId.replace('productPrice', '');
                            if(typeof countdownData[id] != 'undefined') {
                                var countdownHtml = '<div class="product-countdown-wrap countdown-'+id+'">'
                                    +'<div class="count_holder">'
                                    +'<div id="product-countdown-'+id+'"></div>'
                                    +'</div>'
                                    +'</div>';
                                $(this).append(countdownHtml);

                                $('#product-countdown-'+id).countdown(countdownData[id], function (event) {
                                    $(this).text(event.strftime('%D days %H:%M:%S')
                                    );
                                });
                            }
                        });
                    }
                });
            }
        };

        w.ZtVirtuemarter = _ZtVirtuemarter;


        $(document).ready(function(){
            ZtVirtuemarter._init();
        });

    }
})(window, jQuery.noConflict());


/**
 * Zt Virtuemarter ajax cart
 * @param {type} w Windows pointer
 * @param {type} $ jQuery pointer
 * @returns {undefined}
 */
(function (w, $) {

    /**
     * Opencart local object
     * @type object
     */
    var _cart = {
        /**
         * Generate
         * @returns {undefined}
         */
        generate: function () {
            var $cartModule = $(".vmCartModule");
            $.getJSON(window.vmSiteurl + "index.php?option=com_ztvirtuemarter&view=ajaxcart" + window.vmLang,
                function (datas, textStatus) {
                    $cartModule.find(".vm_cart_products .content-top").html("");

                    $('#zt_top_cart .total-price').html(datas.billTotal.replace('Total', ''));
                    $('#cart .total-item').html('(' + datas.totalProduct + ')items - ');

                    var cartItem = '<p class="add-product">Recently added item(s)</p>';
                    $('.vm_cart_products .content-top').append(cartItem);
                    $.each(datas.products, function (key, product) {
                        var item_quantity = (product.quantity > 1) ? '(x' + product.quantity + ')' : '';
                        cartItem = '<div class="ajax-cart-product cart-row cart-row-item-' + key + ' clearfix">';
                        cartItem += '<div class="cart-product-img">';
                        cartItem += product.image;
                        cartItem += '</div>';
                        cartItem += '<div class="cart-product-detail">';
                        cartItem += '<h4>' + product.product_name + item_quantity + '</h4>';
                        cartItem += '<p class="product-price">' + product.subtotal_with_tax + '</p>';
                        cartItem += '<a href="#" onclick="ZtVirtuemarter.cart.remove(' + key + ')" class="cart-ajax-del" data-type="cart-view"><i class="fa fa-times"></i>Remove</a>';
                        cartItem += '</div>';
                        cartItem += '</div>';
                        $('.vm_cart_products .content-top').append(cartItem);

                    });
                    if (datas.totalProduct > 0) {
                        $('.cart-header-info').show();
                    } else {
                        $cartModule.find(".vm_cart_products .content-top").html("<p>You have no items in your shopping cart.</p>");
                        $('.cart-header-info').hide();
                    }

                    $cartModule.find(".show_cart").html(datas.cart_show);
                    $cartModule.find(".total_products").html(datas.totalProductTxt);
                    $cartModule.find(".total").html(datas.billTotal);
                }
            );
        },
        /**
         * Remove item from cart
         * @param {type} cart_id
         * @returns {undefined}
         */
        remove: function (cart_id) {
            var base_url = $('#base_url').val();
            var container = $('#zt_top_cart .zt-cart-inner .cart-row-item-' + cart_id);
            container.addClass('add-position');
            $.ajax({
                type: 'post',
                url: base_url + 'index.php?option=com_virtuemart&view=cart&task=delete',
                data: 'cart_virtuemart_product_id=' + cart_id,
                success: function (html) {
                    ZtVirtuemarter.cart.generate();
                    container.removeClass('add-position');
                }
            });
        }
    };

    /* Append cart to ZtVirtuemarter */
    $(document).ready(function () {
        w.ZtVirtuemarter.cart = _cart;
    });

    /* add item to cart header */
    $(document).ready(function() {
        $.fn.updateVirtueMartCartModule = function (arg) {
            var options = $.extend({}, $.fn.updateVirtueMartCartModule.defaults, arg);
            return this.each(function () {
                $.ajaxSetup({ cache: false })
                ZtVirtuemarter.cart.generate();
            });
        };

        /* Definition Of Defaults */
        $.fn.updateVirtueMartCartModule.defaults = {
            name1: 'value1'
        };
    });

})(window, jQuery);


/**
 * Zt Virtuemarter compare
 * @param {type} w Windows pointer
 * @param {type} $ jQuery pointer
 * @returns {undefined}
 */
(function (w, $) {

    /**
     * Compare local object
     * @type object
     */
    var _compare = {
        /* Element selector */
        _elements: {
            itemCount: '.btn-compare span'
        },

        /**
         * Add item to compare
         * @param {type} id
         * @returns {undefined}
         */
        add: function (id) {
            var _self = this;
            $.fancybox.showActivity();
            $.ajax({
                url: 'index.php?option=com_ztvirtuemarter&view=comparelist&task=add&product_id=' + id,
                type: 'post',
                data: 'product_id=' + id,
                dataType: 'json',
                success: function (json) {
                    $.fancybox({
                        "titlePosition": "inside",
                        "transitionIn": "fade",
                        "transitionOut": "fade",
                        "changeFade": "fast",
                        "type": "html",
                        "autoCenter": true,
                        "closeBtn": false,
                        "closeClick": false,
                        "content":
                            '<div class="compare-product-detail">'
                                + json.img_prod
                                + json.title
                                + json.btnrem
                                + json.message
                                + json.btncompare
                                + json.btncompareback
                                + '</div>'
                    });
                    if (json.totalcompare !== '') {
                        $('#mod_compare .zt-cart-inner .vmproduct .not_text').remove();
                    }
                    if (json.exists != 1) {
                        var compare_item = '<div id="compare_prod_' + json.product_ids + '" class="modcompareprod clearfix">' + json.img_prod + json.prod_name + '</div><div class="clear"></div>';
                        $('#mod_compare .zt-cart-inner .vmproduct').append(compare_item);
                    }
                    _self.updateItemCount(json.totalcompare);
                    $('#compare_continue').click($.fancybox.close);
                }

            });
        },
        /**
         * Remove item from compare
         * @param {type} id
         * @returns {undefined}
         */
        remove: function (id) {
            var _self = this;
            $('#compare_cat' + id + ' a').removeClass('go_to_compare active');
            $.ajax({
                url: 'index.php?option=com_ztvirtuemarter&view=comparelist&task=removed',
                type: 'post',
                data: 'remove_id=' + id,
                dataType: 'json',
                success: function (json) {
                    $('.compare_prod_' + id
                        + ', #compare_prod_' + id
                        + ', .success .successprod_' + id
                        + ', .success_compare span').remove();
                    $('#system_view .success .success_compare').append('<span class="warning">' + json.rem + '</span>');
                    $('.list_compare' + id + ' a').removeClass('go_to_compare active');
                    if (json.totalrem < 1) {
                        $("#mod_compare .not_text").removeClass('displayNone');
                        $("#butseldcomp").addClass('displayNone');
                        $(".module-title.compare.no-products").addClass('displayBlock');
                        $(".browscompare_list").remove();
                        $('#mod_compare .zt-cart-inner .vmproduct').html('<div class="not_text compare">You have no product to compare.</div>');
                    }
                    _self.updateItemCount(json.totalrem);
                    $.fancybox.close();
                }
            });
        },
        /**
         * Update item count
         * @param {type} totalItem
         * @returns {undefined}
         */
        updateItemCount: function (totalItem) {
            $(this._elements.itemCount).html((totalItem < 1) ? '0' : totalItem);
        }
    };

    /* Append wishlist to ZtVirtuemarter */
    $(document).ready(function () {
        w.ZtVirtuemarter.compare = _compare;
    });

})(window, window.jQuery);

/**
 * Zt Virtuemarter quickview
 * @param {type} w Windows pointer
 * @param {type} $ jQuery pointer
 * @returns {undefined}
 */
(function (w, $) {

    /**
     * quickview local object
     * @type object
     */
    var _quickview = {
        show: function(id) {
            $.fancybox.showActivity();
            $.ajax({
                url: 'index.php?option=com_ztvirtuemarter&view=quickview',
                type: 'get',
                data: 'virtuemart_product_id=' + id,
                success: function (data) {
                    $.fancybox({
                        "titlePosition": "inside",
                        "transitionIn": "fade",
                        "transitionOut": "fade",
                        "changeFade": "fast",
                        "type": "html",
                        "autoCenter": true,
                        "closeBtn": false,
                        "closeClick": false,
                        "content": data
                    });
                    $.fn.updateVirtueMartCartModule = function (arg) {
                        var options = $.extend({}, $.fn.updateVirtueMartCartModule.defaults, arg);
                        return this.each(function () {
                            $.ajaxSetup({ cache: false })
                            ZtVirtuemarter.cart.generate();
                        });
                    };

                    /* Definition Of Defaults */
                    $.fn.updateVirtueMartCartModule.defaults = {
                        name1: 'value1'
                    };
                }
            });
        }

    };

    /* Append wishlist to ZtVirtuemarter */
    $(document).ready(function () {
        w.ZtVirtuemarter.quickview = _quickview;
    });

})(window, jQuery);

/**
 * Cena whishlist
 * @param {type} w Windows pointer
 * @param {type} $ jQuery pointer
 * @returns {undefined}
 */
(function (w, $) {

    /**
     * Whishlist local object
     * @type object
     */
    var _wishlist = {
        /* Element selector */
        _elements: {
            itemCount: '.btn-wishlist span'
        },

        /**
         * Add item to whishlist
         * @param {type} id
         * @returns {undefined}
         */
        add: function (id) {
            var _self = this;
            $.fancybox.showActivity();
            $('body > :first-child').prepend('<div class="overlay"></div><div class="loading"></div>');
            $.ajax({
                url: 'index.php?option=com_ztvirtuemarter&view=wishlist&task=add&product_id=' + id,
                type: 'post',
                data: 'product_id=' + id,
                dataType: 'json',
                success: function (json) {
                    $.fancybox({
                        "titlePosition": "inside",
                        "transitionIn": "fade",
                        "transitionOut": "fade",
                        "changeFade": "fast",
                        "type": "html",
                        "autoCenter": true,
                        "closeBtn": false,
                        "closeClick": false,
                        "content":
                            '<div class="wishlist-product-detail">'
                                + json.img_prod
                                + json.title
                                + json.btnrem
                                + json.message
                                + json.btnwishlists
                                + json.btnwishlistsback
                                + '</div>'
                    });
                    if (json.totalwishlists !== '') {
                        _self.updateItemCount(json.totalwishlists);
                        $('#mod_wishlists .vmproduct .not_text').remove();
                    }
                    if (json.exists != 1) {
                        var wishlist_item = '<div class="modwishlistsprod clearfix" id="wishlists_prod_' + json.product_ids + '">' + json.img_prod + json.prod_name + '</div><div class="clear"></div>';
                        $('#mod_wishlists .vmproduct').append(wishlist_item);
                    }
                    $('#wishlists_continue').click($.fancybox.close);
                }
            });
        },
        /**
         * Remove item from whishlist
         * @param {type} id
         * @returns {undefined}
         */
        remove: function (id) {
            var _self = this;
            $.ajax({
                url: 'index.php?option=com_ztvirtuemarter&view=wishlist&task=removed',
                type: 'post',
                data: 'remove_id=' + id,
                dataType: 'json',
                success: function (json) {
                    $('.count_holder_small'
                        + ', #wishlists_prod_' + id
                        + ', .wishlists_prods_' + id
                        + ', .success .successprod_' + id
                        + ', .success_wishlists span').remove();
                    $('#system_view .success .success_wishlists').append('<span class="warning">' + json.rem + '</span>');
                    $('.list_wishlists' + id + ' a').removeClass('go_to_compare active');
                    if (json.totalrem < 1) {
                        $("#mod_wishlists .not_text").removeClass('displayNone');
                        $("#butseldwish").addClass('displayNone');
                        $(".module-title.wishlists.no-products").addClass('displayBlock');
                        $(".category-wishlist").remove();
                        $('#mod_wishlists .vmproduct').html('<div class="not_text wishlists">You have no product to wishlist.</div>');
                    }
                    _self.updateItemCount(json.totalrem);
                    $.fancybox.close();
                }
            });
        },
        /**
         * Update item count
         * @param {type} totalItem
         * @returns {undefined}
         */
        updateItemCount: function (totalItem) {
            $(this._elements.itemCount).html((totalItem < 1) ? '0' : totalItem);
        }
    };

    /* Append wishlist to ZtVirtuemarter */
    $(document).ready(function () {
        w.ZtVirtuemarter.wishlist = _wishlist;
    });

})(window, window.jQuery);