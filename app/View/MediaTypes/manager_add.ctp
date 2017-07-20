<?php ?>
<!-- File: app/View/MediaType/admin_add.ctp -->
<div class="top">
	<h1><?php echo __('Add Media Type'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('MediaType');?>
		    <fieldset>
		        <legend><?php echo __('Add Media Type'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Media Type')); ?>
	</div>
</div>