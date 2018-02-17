/**
 * @package ZT News Module for Joomla! 1.7
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2011- ZooTemplate.com
 * @license JS files are GNU/GPL
**/

var JVNews = new Class({
	options:{
	},
    /**
     *
     * @param options
     */
	initialize:function(options){
		this.setOptions(options);
		var containerId = '#news_slidecontent'+this.options.id;
		var slideItem = $$(containerId + ' ' + '.news_mask'+' '+'.news_img');
		var slideBar = $$(containerId + ' ' + '.news_bar'+' '+'a.item');
		var moduleId = this.options.moduleId;
		var contentCookie='content'+this.options.id;
		var amountCookie = 'amount'+this.options.id;
        // @todo Move selector to internal vars
		$("news_content_category"+this.options.id).getElements('div.cat_addremove a.cat_remove').each(function(item,i){
	 		item.addEvent('click',function(e){
	 			if(e.preventDefault){ e.preventDefault()}
	 			e.returnValue = false;
	 			$('news_section'+this.options.id).setStyle('height','auto');
	 			var minItem = parseInt(this.options.minItem,10);
	 			var itemECat = item.getParent().getParent().getParent().getParent();
	 			if(itemECat.getElements('ul.cat_morelink li.active').length > minItem) {
	 				itemECat.getElements('ul.cat_morelink li.active')[itemECat.getElements('ul.cat_morelink li.active').length - 1].removeClass("active").addClass("block");
	 			}
	 			var strCatCook = this.getCookie("content"+this.options.id);
	 			var strCatContent = '';
	 			var catId = item.getProperty("id").replace("cat_remove","");
	 			aryCat = strCatCook.split(',');
	 			var currentItem = itemECat.getElements('ul.cat_morelink li.active').length;
	 			//Rewrite status of content in category in cookie
	 			aryCat.each(function(cItem,j){
	 				strCatContent+=this.parseCatContent(cItem,catId,currentItem);
			 		if(j != aryCat.length - 1) strCatContent+=',';
	 			}.bind(this));
	 			this.setCookie(contentCookie,strCatContent);
	 		}.bind(this));
		}.bind(this));
        // @todo Move selector to internal vars
		$("news_content_category"+this.options.id).getElements('div.cat_addremove a.cat_add').each(function(item,i){
			item.addEvent('click',function(e){
				if(e.preventDefault){ e.preventDefault()}
				e.returnValue = false;
				$('news_section'+this.options.id).setStyle('height','auto');
				var itemECat = item.getParent().getParent().getParent().getParent();
				if(itemECat.getElements("ul.cat_morelink li.block").length) {
					itemECat.getElements("ul.cat_morelink li.block")[0].removeClass("block").addClass("active");
					var strCatCook = this.getCookie("content"+this.options.id);
		 			var strCatContent = '';
		 			var catId = item.getProperty("id").replace("add_cat","");
		 			aryCat = strCatCook.split(',');
		 			var currentItem = itemECat.getElements('ul.cat_morelink li.active').length;
		 			//Rewrite status of content in category in cookie
		 			aryCat.each(function(cItem,j){
		 				strCatContent+=this.parseCatContent(cItem,catId,currentItem);
				 		if(j != aryCat.length - 1) strCatContent+=',';
		 			}.bind(this));
		 			this.setCookie(contentCookie,strCatContent);
			}
			}.bind(this));
		}.bind(this));
		//Show slide
		this.showSlide(slideBar,slideItem);
		//End slide

		//Accordion option select category in section
		var newsOptionHeight = $('news_option'+this.options.id).offsetHeight;
		$('news_option'+this.options.id).setStyle('height','0px');
		// Show/collapse section
		var newsTitle = $$('#news_title'+this.options.id+' .title')[0];
		newsTitle.addEvent('click',function(){
			if(this.isVisible($('news_section'+this.options.id))){
				newsHeight = $('news_section'+this.options.id).offsetHeight;
				newsTitle.removeClass('expand').addClass('collapse');
				hideOption = new Fx.Morph($('news_section'+this.options.id),{duration: 500, transition: Fx.Transitions.linear}).start({'height': [newsHeight,0]})
			}
			if(this.isHidden($('news_section'+this.options.id))){
				newsTitle.removeClass('collapse').addClass('expand');
				viewOption = new Fx.Morph($('news_section'+this.options.id),{duration: 500, transition: Fx.Transitions.linear}).start({'height': [0,newsHeight]})
			}
			//console.log(newsHeight);
		}.bind(this));
		//End show/collapse
        // @todo Move selector to internal vars
		$('news_edit'+this.options.id).addEvent('click',function(){
			 $('news_section'+this.options.id).setStyle('height','auto');
			//$('news_title'+this.options.id).removeEvent('click',function(){});
			if(this.isVisible($('news_option'+this.options.id))) {
				hideOption = new Fx.Morph($('news_option'+this.options.id),{duration: 400, transition: Fx.Transitions.linear}).start({'height': [newsOptionHeight,0]});
			}
			if(this.isHidden($('news_option'+this.options.id))){
				viewOption = new Fx.Morph($('news_option'+this.options.id), {duration: 400, transition: Fx.Transitions.linear}).start({'height': [0,newsOptionHeight]})
			}
		}.bind(this));
		//End
		$('cancel'+this.options.id).addEvent('click',function(){
			$('news_section'+this.options.id).setStyle('height','auto');
			var newsOptionHeight = $('news_option'+this.options.id).offsetHeight;
			if(this.isVisible($('news_option'+this.options.id))) ztCancel = new Fx.Morph($('news_option'+this.options.id),{duration: 400, transition: Fx.Transitions.linear}).start({'height': [newsOptionHeight,0]})
		}.bind(this));
		//Execute save select category
		//Execute reset
		$('reset'+this.options.id).addEvent('click',function(){
			$('news_section'+this.options.id).setStyle('height','auto');
			//console.log($('no_article3').options);
			urlRequest = this.options.urlAjax+'?action=reset'+'&noHeadline='+this.options.noHeadline+'&noLink='+this.options.noLink+'&fcatId='+this.options.id+'&moduleId='+moduleId;
			var request = new Request.JSON({
				url: ''+urlRequest,
				onComplete:function(jsonObj){
					var strAmount = this.options.noHeadline+','+this.options.noLink;
					$('news_slidecontent'+this.options.id).set('html',jsonObj.strHeadLine);
					$('wrap_news_more'+this.options.id).set('html',jsonObj.strMoreLink);
					$('news_content_category'+this.options.id).set('html','');
					var listChkCat = $$('#news_option'+this.options.id+' form')[0].getElements('input[name=section]');
					listChkCat.each(function(el){
						el.checked = false;
					});
					this.deleteCookie(amountCookie);
					this.deleteCookie('content'+this.options.id);
					this.deleteCookie('sec'+this.options.id);
					this.setCookie(amountCookie,strAmount);
					var noArticle= $('no_article'+this.options.id).options;
					var noMoreLink = $('no_articles_links'+this.options.id).options;
					for(i=0;i<noArticle.length;i++){
						if(noArticle[i].value == this.options.noHeadline) noArticle[i].selected = true;
					}
					for(i=0;i<noMoreLink.length;i++){
						if(noMoreLink[i].value == this.options.noLink) noMoreLink[i].selected = true;
					}
					var slideItem = $$(containerId + ' ' + '.news_mask'+' '+'.news_img');
					var slideBar = $$(containerId + ' ' + '.news_bar'+' '+'a.item');
					this.showSlide(slideBar,slideItem);
					var newsOptionHeight = $('news_option'+this.options.id).offsetHeight;
					if(this.isVisible($('news_option'+this.options.id))) ztreset = new Fx.Morph($('news_option'+this.options.id),{duration: 400, transition: Fx.Transitions.linear}).start({'height': [newsOptionHeight,0]})
				}.bind(this),
				onRequest:function(){
				}
			}).send();
		}.bind(this));
		//End reset
		$('save'+this.options.id).addEvent('click',function(){
			var strCat = '';
			var chkCat = $$('#news_option'+this.options.id+' form')[0].getElements('input[name=section]');
			chkCat.each(function(el){
				if(el.checked == true){
					strCat = strCat+el.getProperty('value')+',';
				}
			});
			var secCookie ='sec'+this.options.id;
			var noHeadline = $('no_article'+this.options.id).getProperty('value');
			var noLink = $('no_articles_links'+this.options.id).getProperty('value');
			if(strCat) urlRequest = this.options.urlAjax+'?fcatId='+this.options.id+"&catId="+strCat+"&moduleId="+moduleId+'&noHeadline='+noHeadline+'&noLink='+noLink+'&action=save';
			else urlRequest = this.options.urlAjax+'?fcatId='+this.options.id+"&moduleId="+moduleId+'&noHeadline='+noHeadline+'&noLink='+noLink+'&action=save';
			var request = new Request.JSON({
				url: ''+urlRequest,
				onComplete:function(jsonObj){
					$('news_section'+this.options.id).setStyle('height','auto');
					$('news_slidecontent'+this.options.id).set('html',jsonObj.strHeadLine);
					$('wrap_news_more'+this.options.id).set('html',jsonObj.strMoreLink);
					$('news_content_category'+this.options.id).set('html',jsonObj.strItem);
					var slideItem = $$(containerId + ' ' + '.news_mask'+' '+'.news_img');
					var slideBar = $$(containerId + ' ' + '.news_bar'+' '+'a.item');
					this.showSlide(slideBar,slideItem);
					var strAmount = noHeadline+','+noLink;
					this.deleteCookie(amountCookie);
					this.setCookie(amountCookie,strAmount);
					if(strCat !=''){
						//Set cookie for section and category
						strSecCookie = strCat.lastIndexOf(',');
						strCat = strCat.substring(0,strSecCookie);
						var aryCat = strCat.split(',');
						var strCatContent = '';
						aryCat.each(function(item,i){
							strCatContent+=item+'_'+'3';
							if(i != aryCat.length - 1) strCatContent+=',';
						});
						this.setCookie(secCookie,strCat);
						this.setCookie(contentCookie,strCatContent);
						//End cookie
						 $("news_content_category"+this.options.id).getElements('div.cat_addremove a.cat_remove').each(function(item,i){
							item.addEvent('click',function(e){
								if(e.preventDefault){ e.preventDefault()}
								e.returnValue = false;
								$('news_section'+this.options.id).setStyle('height','auto');
								var minItem = parseInt(this.options.minItem,10);
								var itemECat = item.getParent().getParent().getParent().getParent();
								if(itemECat.getElements('ul.cat_morelink li.active').length > minItem) {
									itemECat.getElements('ul.cat_morelink li.active')[itemECat.getElements('ul.cat_morelink li.active').length - 1].removeClass("active").addClass("block");
								}
								var strCatCook = this.getCookie("content"+this.options.id);
								var strCatContent = '';
								var catId = item.getProperty("id").replace("cat_remove","");
								aryCat = strCatCook.split(',');
								var currentItem = itemECat.getElements('ul.cat_morelink li.active').length;
								//Rewrite status of content in category in cookie
								aryCat.each(function(cItem,j){
									strCatContent+=this.parseCatContent(cItem,catId,currentItem);
									if(j != aryCat.length - 1) strCatContent+=',';
								}.bind(this));
								this.setCookie(contentCookie,strCatContent);
							}.bind(this));
						}.bind(this));
						$("news_content_category"+this.options.id).getElements('div.cat_addremove a.cat_add').each(function(item,i){
							item.addEvent('click',function(e){
							if(e.preventDefault){ e.preventDefault()}
							e.returnValue = false;
							$('news_section'+this.options.id).setStyle('height','auto');
							var itemECat = item.getParent().getParent().getParent().getParent();
							if(itemECat.getElements("ul.cat_morelink li.block").length) {
									itemECat.getElements("ul.cat_morelink li.block")[0].removeClass("block").addClass("active");
									var strCatCook = this.getCookie("content"+this.options.id);
									var strCatContent = '';
									var catId = item.getProperty("id").replace("add_cat","");
									aryCat = strCatCook.split(',');
									var currentItem = itemECat.getElements('ul.cat_morelink li.active').length;
									//Rewrite status of content in category in cookie
									aryCat.each(function(cItem,j){
										strCatContent+=this.parseCatContent(cItem,catId,currentItem);
										if(j != aryCat.length - 1) strCatContent+=',';
									}.bind(this));
									this.setCookie(contentCookie,strCatContent);
							}
						}.bind(this));
						}.bind(this));
					} else {
						this.deleteCookie(contentCookie);
						this.deleteCookie(secCookie);
					}
				}.bind(this),
				onRequest:function(){}
			}).send();
			var newsOptionHeight = $('news_option'+this.options.id).offsetHeight;
			if(this.isVisible($('news_option'+this.options.id))) visible = new Fx.Morph($('news_option'+this.options.id),{duration: 400, transition: Fx.Transitions.linear}).start({'height': [newsOptionHeight,0]})
			}.bind(this));
		//End
	},
    /**
     *
     * @param item
     * @param catId
     * @param currentItem
     * @returns {string|*}
     */
	parseCatContent:function(item,catId,currentItem){
		pos = item.lastIndexOf('_');
		cId = item.substring(0,pos);
		noItem = item.substring(pos+1,item.length);
		if(cId.toString() == catId.toString()) noItem = currentItem;
		strCatContent= cId+'_'+noItem;
		return strCatContent;
	},
    /**
     *
     * @param key
     * @param value
     */
	setCookie:function(key,value){
		var expires = new Date();
		expires.setTime(expires.getTime() + 31536000000); //1 year
		document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
	},
    /**
     *
     * @param key
     * @returns {null}
     */
	getCookie:function(key){
		var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
		return keyValue ? keyValue[2] : null;
	},
    /**
     *
     * @param key
     */
	deleteCookie:function(key){
		 document.cookie = key +'=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
	},
    /**
     *
     * @param obj
     * @returns {boolean}
     */
	isDisplayed:function(obj){
		return obj.getStyle("display")!="none";
	},
    /**
     *
     * @param slideBar
     * @param slideItem
     */
	showSlide:function(slideBar,slideItem){
		slideBar.each(function(item,i){
			item.addEvent('mouseenter',function(){
				slideItem.each(function(item1,j){
					if(i!=j){
						slideBar[j].removeClass("selected");
						item1.addClass("block").removeClass("active");
					} else {
						item.addClass("selected");
						item1.removeClass("block").addClass("active");
					}
				});
			}.bind(this));
		}.bind(this));
	},
    /**
     *
     * @param obj
     * @returns {boolean}
     */
	isVisible:function(obj){
		var a=obj.offsetWidth,b=obj.offsetHeight;
		return(a==0 || b==0)? false:(a>0 && b>0) ? true : obj.getStyle('display') !== 'none';
	},
    /**
     *
     * @param obj
     * @returns {boolean}
     */
	isHidden:function(obj){
		return !this.isVisible(obj);
	}
});
JVNews.implement(new Events);
JVNews.implement(new Options);
