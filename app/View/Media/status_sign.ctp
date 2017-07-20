<?php 

$sign = ($this->request->data['SanitizeStatus']['order'] == 2?__('Sign'):__('Re-Sign'));
?>
<div class="top">
	<h1><?php echo  __('%s the %s: Serial Number: %s', $sign, __('Media'), $media['Media']['serial_number']); ?></h1>
	<div class="page_options">
	
		
		<ul>
			<li><?php echo $this->Html->link(__('Download PDF Form to %s', $sign), array('action' => 'get_form', $this->passedArgs[0]), array('id' => 'getform')); ?></li>
		</ul>
	</div>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Media', array('type' => 'file'));?>
		    <fieldset>
		    	<?php
					
					$max_upload = (int)(ini_get('upload_max_filesize'));
					$max_post = (int)(ini_get('post_max_size'));
					$memory_limit = (int)(ini_get('memory_limit'));
					$upload_mb = min($max_upload, $max_post, $memory_limit);
					
					echo $this->Form->input('id', array('type' => 'hidden'));
					echo $this->Form->input('serial_number', array('type' => 'hidden'));
					
					echo $this->Form->input('file', array(
						'label' => __('Upload %s %s for %s', __('Signed'), __('PDF Form'), __('Media')),
						'type' => 'file',
						'between' => __('(Max file size is %sM).', $upload_mb),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Upload and %s this %s', $sign, __('Media'))); ?>
	</div>
</div>
<?php if ($this->request->is('post') || $this->request->is('put')): ?>
<?php else: ?>
<script type="text/javascript">
//<![CDATA[
$("document").ready(function() {
	var download_url = $("a#getform").attr('href');
	if(download_url)
		window.location.href = download_url;
}); // ready
</script>
<?php endif; ?>

