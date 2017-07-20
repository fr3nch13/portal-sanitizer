<?php ?>
<div class="top">
	<h1><?php echo __('Change %s Test Results', __('Media')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media');?>
		    <fieldset>
		        <?php
					echo $this->Form->input('id', array('type' => 'hidden'));
					
					echo $this->Form->input('Media.tested', array(
						'label' => array(
							'text' => __('%s Test Results', __('Media')),
						),
						'options' => $this->Local->tested(false, true),
						'multiple' => false,
					));
		        ?>
		    </fieldset>
		<?php echo $this->Form->end(__('Update %s', __('Media'))); ?>
	</div>
</div>
