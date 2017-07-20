<?php 
?>
<div class="top">
	<h1><?php echo __('Mark %s as Repurposed', __('Media')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media');?>
		    <fieldset>
		        <?php
					echo $this->Form->input('Media.id', array('type' => 'hidden'));
					
					echo $this->Form->input('Media.repurpose_status_id', array(
						'label' =>  __('Repurpose %s To:', __('Media')),
						'options' => $repurpose_statuses,
						'multiple' => false,
					));
					
					echo $this->Form->input('Media.repurposed_notes', array(
						'label' => array(
							'text' => __('Notes (Any notes/details you would like to include.)'),
						),
						'type' => 'textarea',
					));
		        ?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>
