<?php ?>
<div class="top">
	<h1><?php echo __('Add Sanitized %s', __('Media')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media', array('type' => 'file', 'id' => 'add_form'));?>
		    <fieldset>
		        <legend class="section"><?php echo __('Primary Details (required)'); ?></legend>
		        <?php
					
					echo $this->Form->input('Media.serial_number', array(
						'label' => array(
							'text' => __('%s Serial Number', __('Media')),
							'required' => true,
						),
						'div' => array('class' => 'third required'),
						'required' => true,
						'id' => 'serial_number',
					));
					
					echo $this->Form->input('Media.sanitize_status_id', array(
						'label' => array(
							'text' => __('%s Status', __('Sanitize')),
							'required' => true,
						),
						'options' => $sanitize_statuses,
						'div' => array('class' => 'third required'),
						'required' => true,
					));
					
					echo $this->Form->input('Media.media_type_id', array(
						'label' => array(
							'text' => __('%s Type', __('Media')),
						),
						'options' => $media_types,
						'multiple' => false,
						'div' => array('class' => 'third'),
					));
		        ?>
		    </fieldset>
		    <fieldset>
		        <legend class="section"><?php echo __('Extra Details (optional)'); ?></legend>
		    	<?php
					
					echo $this->Form->input('Media.asset_tag', array(
						'label' => array(
							'text' => __('Associated Asset Tag'),
						),
						'div' => array('class' => 'forth'),
					));
					
					echo $this->Form->input('Media.example_ticket', array(
						'label' => array(
							'text' => __('Example Ticket'),
							'required' => false,
						),
						'div' => array('class' => 'forth'),
						'required' => false,
					));
					
					echo $this->Form->input('Media.tickets', array(
						'label' => array(
							'text' => __('Other Related Tickets'),
						),
						'div' => array('class' => 'forth'),
						'type' => 'text',
					));
					
					echo $this->Form->input('Media.obtained', array(
						'label' => array(
							'text' => __('Date %s was Obtained', __('Media')),
						),
						'div' => array('class' => 'forth'),
						'type' => 'date',
					));
					
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('Media.form', array(
						'label' => array(
							'text' => __('Form Factor'),
						),
						'div' => array('class' => 'third'),
						'type' => 'text',
					));
					
					echo $this->Form->input('Media.size', array(
						'label' => array(
							'text' => __('Size'),
						),
						'div' => array('class' => 'third'),
						'type' => 'text',
					));
					
					echo $this->Form->input('Media.manufacturer', array(
						'label' => array(
							'text' => __('Manufacturer'),
						),
						'div' => array('class' => 'third'),
						'type' => 'text',
					));
					
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('Media.details', array(
						'label' => array(
							'text' => __('Notes (Any notes/details you would like to include.)'),
						),
						'type' => 'textarea',
					));
					
					echo $this->Wrap->divClear();
					
					echo $this->Tag->autocomplete();
		        ?>
		    </fieldset>
		    <fieldset>
		        <legend class="section"><?php echo __('Related Attachment (optional)'); ?></legend>
		    	<?php
					
					$max_upload = (int)(ini_get('upload_max_filesize'));
					$max_post = (int)(ini_get('post_max_size'));
					$memory_limit = (int)(ini_get('memory_limit'));
					$upload_mb = min($max_upload, $max_post, $memory_limit);
					
					echo $this->Form->input('file', array(
						'type' => 'file',
						'between' => __('(Max file size is %sM).', $upload_mb),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Media'))); ?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	var checkSerialNumber = function( input ) {
	// check to make sure the serial number wasn't entered in twice
		var alert_them = false;
		var value_fixed = false;
		var value = $(input).val();
		
		// entered twice
		if(value.length && (value.length % 2) == 0)
		{
			// entered twice
			var halflength = (value.length / 2);
			var part1 = value.substring(0, halflength);
			var part2 = value.substring(halflength);
			if(part1 == part2)
			{
				alert_them = true;
				value_fixed = part1;
			}
		}
		/// entered 3 times
		if(value.length && (value.length % 3) == 0)
		{
			// entered twice
			var thirdlength = (value.length / 3);
			var part1 = value.substring(0, thirdlength);
			var part2 = value.substring(thirdlength, (thirdlength + thirdlength));
			var part3 = value.substring((thirdlength + thirdlength));
			if(part1 == part2 && part1 == part3)
			{
				alert_them = true;
				value_fixed = part1;
			}
		}
		
		if(alert_them && value_fixed)
		{
			var answer = confirm("<?php echo __('You may have entered the %s multiple times. Do you want to keep it like this (Cancel), or should I fix it (OK)?', __('Serial Number')); ?>");
			// fix it for them
			if(answer)
			{
				$(input).val(value_fixed);
			}
		}
	
	}
	// add_form
	
	$('#add_form').on('submit', function(){
		var serialNumberInput = $(this).find('#serial_number');
		if(serialNumberInput)
			checkSerialNumber(serialNumberInput);
	});
	
	$('#serial_number').on('blur', function(){
		checkSerialNumber($(this));
	});
});
//]]>
</script>
