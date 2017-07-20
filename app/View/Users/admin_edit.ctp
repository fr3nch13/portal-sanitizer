<?php 
// File: app/View/Users/admin_edit.ctp
?>

<div class="top">
	<h1><?php echo __('Edit User'); ?></h1>
</div>

<div class="center">
	<div class="form">
		<?php echo $this->Form->create('User'); ?>
		<fieldset>
			<legend><?php echo __('Edit User Details'); ?></legend>
			<?php
				echo $this->Form->input('id', array('type' => 'hidden'));
//				echo $this->Form->input('name');
//				echo $this->Form->input('email');
//				echo $this->Form->input('role', array('options' => $this->Wrap->userRoles()));
				
				echo $this->Form->input('paginate_items', array(
					'between' => $this->Html->para('form_info', __('How many items should show up in a table by default.')),
					'options' => array(
						'10' => '10',
						'25' => '25',
						'50' => '50',
						'100' => '100',
						'150' => '150',
						'200' => '200',
					),
				));
				?>
		</fieldset>
		<?php echo $this->Form->end(__('Save User Details'));?>
	</div>
</div>