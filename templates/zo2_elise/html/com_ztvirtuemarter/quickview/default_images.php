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


if (!empty($this->product->images)) :

  $image = $this->product->images[0];
  echo $image->displayMediaFull('id="image-zoom-product" data-zoom-image="'.JUri::root().$image->file_url.'"' ,FALSE);

endif;

