<?php ?>
<!-- File: app/View/MediaType/admin_edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit Media Type'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('MediaType');?>
		    <fieldset>
		        <legend><?php echo __('Edit Media Type'); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Media Type')); ?>
	</div>
</div>