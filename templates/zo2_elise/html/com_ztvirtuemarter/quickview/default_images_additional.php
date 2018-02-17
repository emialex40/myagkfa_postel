<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$start_image = VmConfig::get('add_img_main', 0) ? 0 : 1;

for ($i = $start_image - 1; $i < count($this->product->images); $i++) :
    $image = $this->product->images[$i];
    echo $image->displayMediaFull('id="image-zoom-product_'.$i.'"',FALSE);
endfor;
?>
