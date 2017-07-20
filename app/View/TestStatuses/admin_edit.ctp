<?php 
// File: app/View/TestStatus/admin_edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Test Status')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('TestStatus');?>
		    <fieldset>
		        <legend><?php echo __('Edit %s', __('Test Status')); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Test Status'))); ?>
	</div>
</div>