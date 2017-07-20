<?php 
?>
<div class="top">
	<h1><?php echo __('Revert %s Status', __('Media')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media');?>
		    <fieldset>
		        <?php
					echo $this->Form->input('Media.id', array('type' => 'hidden'));
					
					echo $this->Form->input('Media.sanitize_status_id', array(
						'label' => array(
							'text' => __('Change %s Status to:', __('Media')),
						),
						'options' => $sanitize_statuses,
						'multiple' => false,
					));
		        ?>
		    </fieldset>
		<?php echo $this->Form->end(__('Revert %s Status', __('Media'))); ?>
	</div>
</div>
