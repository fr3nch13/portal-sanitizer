<?php 
?>
<div class="top">
	<h1><?php echo __('Mark %s as Tested', __('Media')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media');?>
		    <fieldset>
		        <?php
					echo $this->Form->input('Media.id', array('type' => 'hidden'));
					
					echo $this->Form->input('Media.test_status_id', array(
						'label' =>  __('Test Result Status'),
						'options' => $test_statuses,
						'multiple' => false,
					));
					
					echo $this->Form->input('Media.test_notes', array(
						'label' =>  __('Test Notes'),
					));
		        ?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>
