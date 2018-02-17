<?php
//if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
defined( '_JEXEC' ) or die( 'Restricted access' );

class breadzConf{
	protected static $options=array();
	
	static function setOptions($_options){
		self::$options=$_options;
	}
	
	static function option($key){
		return self::$options[$key];
	}
	
	static function set($key, $value){
		self::$options[$key]=$value;
	}
	
}
?>