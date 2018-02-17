<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class chpBreadzWriter {
	
	public static function printBreadcrumbs($elements) {
		$last = count($elements) - 1;
		if (breadzConf::option('startfrom') > $last) return;

		echo '<div class="breadz">';
		
		foreach ($elements as $i => $element) {
			if ($i != 0) {
				echo '<span> &rsaquo; </span>';
			}
			
			if ($element['url'] && $i != $last) {
				echo '<a href="'.$element['url'].'">'. $element['name'] .'</a>';
			} else {
				echo '<span>'. $element['name'] .'</span>';
			}
			
			
			if ($element['xurl'] && breadzConf::option('showx')) {
				echo ' <sup><a href="'.$element['xurl'].'" title="'. JText::_('BR_REMOVE_SELECTION') .'">(x)</a></sup>';
			}
			
			
			
			
		}
		//echo '<pre>';
		//var_dump($elements);
		//print_r($elements);
		
		echo '</div>';
	}

}

?>