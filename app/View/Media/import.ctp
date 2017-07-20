<?php ?>
<div class="top">
	<h1><?php echo __('Import Sanitized %s', __('Media')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media');?>
		    <fieldset>
		    	<?php
					
					echo $this->Form->input('Media.import', array(
						'label' => array(
							'text' => __('Media Details'),
						),
						'between' => $this->Html->tag('p', __('Copy and Paste all of the information regarding the multiple Media in this field.')),
						'type' => 'textarea',
					));
		        ?>
		    </fieldset>
		<?php echo $this->Form->end(__('Import %s', __('Media'))); ?>
	</div>
</div>
