<?php 
// File: app/View/Media/view.ctp
$media_status = $this->Local->status($media['SanitizeStatus']['order']);

$tmp = array('User' => $media['MediaAddedUser']);
$MediaAddedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  

$MediaModifiedUser = '';
if($media['MediaModifiedUser']['id'])
{
	$tmp = array('User' => $media['MediaModifiedUser']);
	$MediaModifiedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}

$details_left = array();
$details_right = array();

$details_left[] = array('name' => __('ID'), 'value' => $media['Media']['id']);
$details_left[] = array('name' => __('Serial Number'), 'value' => $media['Media']['serial_number']);
$details_left[] = array('name' => __('%s Status', __('Sanitize')), 'value' => $media['SanitizeStatus']['name']);
$details_left[] = array('name' => __('Associated Asset Tag'), 'value' => $media['Media']['asset_tag']);
$details_left[] = array('name' => __('Example Ticket'), 'value' => $media['Media']['example_ticket']);
$details_left[] = array('name' => __('Related Tickets'), 'value' => $media['Media']['tickets']);
$details_left[] = array('name' => __('Related Attachment'), 'value' => $this->Html->link($media['Media']['filename'], array('action' => 'download', $media['Media']['id'])) );
$details_left[] = array('name' => __('Form Factor'), 'value' => $media['Media']['form']);
$details_left[] = array('name' => __('Size'), 'value' => $media['Media']['size']);
$details_left[] = array('name' => __('Manufacturer'), 'value' => $media['Media']['manufacturer']);
$details_left[] = array('name' => __('Media Type'), 'value' => $media['MediaType']['name']);

$details_right[] = array('name' => __('Obtained'), 'value' => $this->Wrap->niceTime($media['Media']['obtained']));
$details_right[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($media['Media']['created']));
$details_right[] = array('name' => __('Created By'), 'value' => $MediaAddedUser);
$details_right[] = array('name' => __('Last Updated'), 'value' => $this->Wrap->niceTime($media['Media']['modified']));
$details_right[] = array('name' => __('Last Updated By'), 'value' => $MediaModifiedUser);

///// ATLEAST Created - this should always show. placing this stuff here for consistancy
if($media['SanitizeStatus']['order'] >= 1)
{
	$MediaOpenedUser = '';
	if($media['MediaOpenedUser']['id'])
	{
		$tmp = array('User' => $media['MediaOpenedUser']);
		$MediaOpenedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	$details_right[] = array('name' => __('Opened'), 'value' => $this->Wrap->niceTime($media['Media']['opened_date']));
	$details_right[] = array('name' => __('Opened By'), 'value' => $MediaOpenedUser);
}
///// Ready to Sign
if($media['SanitizeStatus']['order'] >= 2)
{
	$MediaSanitizedUser = '';
	if($media['MediaSanitizedUser']['id'])
	{
		$tmp = array('User' => $media['MediaSanitizedUser']);
		$MediaSanitizedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	$details_right[] = array('name' => __('Sanitized'), 'value' => $this->Wrap->niceTime($media['Media']['sanitized_date']));
	$details_right[] = array('name' => __('Sanitized By'), 'value' => $MediaSanitizedUser);
}

///// ATLEAST SIGNED
$signed_form = '';
if($media['SanitizeStatus']['order'] >= 3) // at least signed
{
	$signed_form = $this->Html->link($media['Media']['form_filename'], array('action' => 'download', $media['Media']['id'], 0, $media['Media']['form_filename']));
	
	$MediaSignedUser = '';
	if($media['MediaSignedUser']['id'])
	{
		$tmp = array('User' => $media['MediaSignedUser']);
		$MediaSignedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$details_left[] = array('name' => __('Signed Form'), 'value' => $signed_form  );
	$details_right[] = array('name' => __('Signed'), 'value' => $this->Wrap->niceTime($media['Media']['signed_date']));
	$details_right[] = array('name' => __('Signed By'), 'value' => $MediaSignedUser);
	$details_right[] = array('name' => __('Signature Name'), 'value' => $media['Media']['signer_signature']);
	$details_right[] = array('name' => __('Signature Date'), 'value' => $this->Wrap->niceTime($media['Media']['signer_signed_date']));
	
}
///// ATLEAST released
if($media['SanitizeStatus']['order'] >= 4)
{
	$MediaReleasedUser = '';
	if($media['MediaReleasedUser']['id'])
	{
		$tmp = array('User' => $media['MediaReleasedUser']);
		$MediaReleasedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$details_right[] = array('name' => __('Last Released'), 'value' => $this->Wrap->niceTime($media['Media']['released_date']));
	$details_right[] = array('name' => __('Last Released By'), 'value' => $MediaReleasedUser);
}

// tested, this is separate from above
if($media['MediaTestedUser']['id'])
{
	$tmp = array('User' => $media['MediaTestedUser']);
	$MediaTestedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	
	$details_right[] = array('name' => __('Tested'), 'value' => $this->Wrap->niceTime($media['Media']['tested_date']));
	$details_right[] = array('name' => __('Tested By'), 'value' => $MediaTestedUser);
	$details_right[] = array('name' => __('Test Results'), 'value' => $media['TestStatus']['name']);
	$details_right[] = array('name' => __('Test Notes'), 'value' => $media['Media']['test_notes']);
}

// repurposed, this is separate from above
if($media['MediaRepurposedUser']['id'])
{
	$tmp = array('User' => $media['MediaRepurposedUser']);
	$MediaRepurposedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	
	$details_right[] = array('name' => __('Repurposed'), 'value' => $this->Wrap->niceTime($media['Media']['repurposed_date']));
	$details_right[] = array('name' => __('Repurposed By'), 'value' => $MediaRepurposedUser);
	$details_right[] = array('name' => __('Repurposed Status'), 'value' => $media['RepurposeStatus']['name']);
	$details_right[] = array('name' => __('Repurposed Notes'), 'value' => $this->Wrap->descView($media['Media']['repurposed_notes']));
}

//////////////////

?>
	<div class="left">
	<?php 
	if($details_left and is_array($details_left)) 
		echo $this->element('Utilities.details', array(
			'title' => __('Details'),
			'details' => $details_left,
		)); 
	?>
	</div>
	
	<div class="right">
	<?php 
	if($details_right and is_array($details_right)) 
		echo $this->element('Utilities.details', array(
			'title' => __('Milestones'),
			'details' => $details_right,
		)); 
	?>
	</div>