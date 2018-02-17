<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldMarkers extends JFormField{
    protected $type = 'markers';
      
    protected function getLabel(){
    	return '';
    	
    } 
    protected function  getInput() {
    	
		$document = JFactory::getDocument();
		$document->addStyleDeclaration(
			'table.marker > tbody > tr > td{}'.
			'input.lat, input.lng{width:65px;}'.
			'input.address{width:350px;}'.
			'input.color{width:80px;}'.
			'div.control-group > div.controls{margin-left:10px;}'
		);	
		$document->addStyleSheet( 'http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
		$document->addScript('http://api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU');
	
		$mod_id = isset($_GET['id']) ? $_GET['id'] : 1;
		$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__modules')
			->where("id={$mod_id}");
			$db->setQuery($query);
			$array =  $db->loadAssoc();
			$fields =  json_decode($array['params']); 
		$html = '<div>';
		$html.= JText::_('MOD_YANDEX_MAP_MARKER_MANAGER_DESC').'</br>';
		$html.= "<div><input class='btn bnt-success' type='button' id='add' value='".JText::_('MOD_YANDEX_MAP_ADD')."'/></div>";
		$html.= '<div>';
		$html.= '<table class="marker table table-striped">';	
		$html.= '<thead><tr><th>'.JText::_('MOD_YANDEX_MAP_ADDRESS').'</th><th>'.JText::_('MOD_YANDEX_MAP_LAT').'</th><th>'.JText::_('MOD_YANDEX_MAP_LNG').'</th><th>'.JText::_('MOD_YANDEX_MAP_COLOR').'</th><th>'.JText::_('MOD_YANDEX_MAP_INFO').'</th><th></th></tr></thead>';	
		$html.= '<tbody id="markers">';
		if (isset($fields->marker))
		{	
			for ($i=0;$i< count($fields->marker->address);$i++)
			{	
				$html.='<tr>';
				$html.='<td><input type="text"  class="address" value="'.$fields->marker->address[$i].'" name="jform[params][marker][address][]"/></td>';
				$html.='<td><input type="text" class="lat" value="'.$fields->marker->lat[$i].'" name="jform[params][marker][lat][]"/></td>';
				$html.='<td><input type="text" class="lng" value="'.$fields->marker->lng[$i].'" name="jform[params][marker][lng][]"/></td>';
				$html.='<td><input type="text"  class="color input-colorpicker" value="'.$fields->marker->color[$i].'" name="jform[params][marker][color][]"/></td>';
				$html.='<td><textarea name="jform[params][marker][info][]" >'.$fields->marker->info[$i].'</textarea></td>';
				$html.='<td><i class="fa fa-trash-o fa-2x"></td>';
				$html.='</tr>';	
			}			
		}	
		$html.= '</tbody>';
		$html.= '</table>';	
		$html.= "</div>";
		$html.= "</div>";
        return $html;
    }
	
 }?>
<script type="text/javascript">
	window.onload = function()
	{
		var add = document.getElementById("add");
		add.onclick = function(){
			var tr = document.createElement('tr');
			tr.innerHTML='<td><input type="text" class="address" name="jform[params][marker][address][]"/></td><td><input type="text" class="lat" name="jform[params][marker][lat][]"/></td><td><input type="text" class="lng" name="jform[params][marker][lng][]"/></td><td><input type="text" class="color input-colorpicker" value="#FF0000" name="jform[params][marker][color][]"/></td><td><textarea name="jform[params][marker][info][]"/></textarea></td><td><i class="fa fa-trash-o fa-2x"></td>';
			var tbody=document.getElementById("markers");
			tbody.insertBefore(tr, tbody.firstChild);
			var icontrash = tr.getElementsByTagName("i");
			icontrash[0].onclick = function(){
				this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
			}
			var addr = tr.children[0].getElementsByTagName("input");
			addr[0].onchange = function(){
				var last = document.getElementsByClassName("current");
				last.className = "";
				var current = this.parentNode.parentNode;
				current.className = "current";
				codeAddress(this.value);	
			}		
			//jQ( "table.marker > tbody" ).prepend('<tr><td><input type="text" class="address" name="jform[params][marker][address][]"/></td><td><input type="text" class="lat" name="jform[params][marker][lat][]"/></td><td><input type="text" class="lng" name="jform[params][marker][lng][]"/></td><td><input type="text" class="icon" name="jform[params][marker][icon][]"/></td><td><textarea name="jform[params][marker][info][]"/></textarea></td><td><i class="icon-trash"></td></tr>');	
		}	
		
		var trash = document.getElementsByClassName("fa-trash-o");
		for (var i=0; i < trash.length; i++) {
			trash[i].onclick = function(){
				this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
			}		
		}	
		var address = document.getElementsByClassName("address");
		for (var i=0; i < address.length; i++) {
			address[i].onchange = function(){
				var last = document.getElementsByClassName("current");
				if (last.length > 0)
				{
					last[0].className = "";
				}	
				console.log(last);
				var current = this.parentNode.parentNode;
				current.className = "current";
				codeAddress(this.value);	
			}		
		}
		function codeAddress(address) {		

			ymaps.geocode(address, {results: 1 }).then(function (res) {

			
            var firstGeoObject = res.geoObjects.get(0);

            var    coords = firstGeoObject.geometry.getCoordinates();
			var tr = document.getElementsByClassName("current");
					var lat = tr[0].children[1].getElementsByTagName("input");
					lat[0].value = coords[0];
					var lng = tr[0].children[2].getElementsByTagName("input");
					lng[0].value = coords[1];
			});
		}		
	}	
</script>	