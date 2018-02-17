<?php defined('_JEXEC') or die; ?>
<style>
	#dialog_copy_move label {display: inline-block;}
</style>
<div id="dialog_copy_move" data-textclose="<?php echo JText::_("Close",'revslider')?>" data-textupdate="<?php echo JText::_("Do It!",'revslider')?>" title="<?php echo JText::_("Copy / move slide",'revslider')?>" style="display:none">
	
	<br>
	
	<?php echo JText::_("Choose Slider",'revslider')?>:
	<?php echo $this->selectSliders; ?>
	
	<br><br>
	
	<?php echo JText::_("Choose Operation",'revslider')?>:
	
	<input type="radio" id="radio_copy" value="copy" name="copy_move_operation" checked />
	<label for="radio_copy" style="cursor:pointer;"><?php echo JText::_("Copy",'revslider')?></label>
	&nbsp; &nbsp;
	<input type="radio" id="radio_move" value="move" name="copy_move_operation" />
	<label for="radio_move" style="cursor:pointer;"><?php echo JText::_("Move",'revslider')?></label>		
	
</div>