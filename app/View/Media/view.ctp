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

$page_options = array();
$page_options_admin = array();
$details_left = array();
$details_right = array();
$stats = array();
$tabs = array();

$details_left[] = array('name' => __('ID'), 'value' => $media['Media']['id']);
$details_left[] = array('name' => __('Serial Number'), 'value' => $media['Media']['serial_number']);
$details_left[] = array('name' => __('%s Status', __('Sanitize')), 'value' => $media['SanitizeStatus']['name']);
$details_left[] = array('name' => __('Associated Asset Tag'), 'value' => $media['Media']['asset_tag']);
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


$status_steps = $this->Local->statusBarSteps($media['SanitizeStatus']['order'], $media['RepurposeStatus']['id']);

///// Created
if($media['SanitizeStatus']['order'] == 1)
{
	$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $media['Media']['id']));
	
	// make the next status clickable to activate
	$step_action = 'status_sanitized';
	$status_steps[($media['SanitizeStatus']['order']+1)]['title'] = $this->Html->link(
		$status_steps[($media['SanitizeStatus']['order']+1)]['title'], 
		array('action' => $step_action, $media['Media']['id']),
		array(
			'title' => __('Mark %s as %s', __('Media'), $status_steps[($media['SanitizeStatus']['order']+1)]['title']),
			'confirm' => __('Are you sure you want to do this?'),
		)
	);
		
	$page_options[] = $this->Html->link(__('Mark as %s', __('Sanitized')), array('action' => $step_action, $media['Media']['id']));
}
///// Ready to Sign
elseif($media['SanitizeStatus']['order'] == 2)
{
	$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $media['Media']['id']));
	
	if(!$media['Media']['repurpose_status_id'])
	{
		$page_options[] = $this->Html->link(__('Repurpose'), array('action' => 'mark_repurposed', $media['Media']['id']));
	}
	else
	{
		$page_options[] = $this->Html->link(__('Change/Update %s Status', __('Repurpose')), array('action' => 'mark_repurposed', $media['Media']['id']));
		if(in_array(AuthComponent::user('role'), array('admin')))
		{
			$page_options[] = $this->Html->link(__('Remove from Repurposed'), array('action' => 'unrepurpose', $media['Media']['id'], 'admin' => true));
		}
	}
	
	if(AuthComponent::user('signer') and !$media['Media']['repurpose_status_id'])
	{
		// make the next status clickable to activate
		$step_action = 'status_sign';
		$status_steps[($media['SanitizeStatus']['order']+1)]['title'] = $this->Html->link(
			$status_steps[($media['SanitizeStatus']['order']+1)]['title'], 
			array('action' => $step_action, $media['Media']['id']),
			array(
				'title' => __('Mark %s as %s', __('Media'), $status_steps[($media['SanitizeStatus']['order']+1)]['title']),
				'confirm' => __('Are you sure you want to do this?'),
			)
		);
		$page_options[] = $this->Html->link(__('Sign'), array('action' => $step_action, $media['Media']['id']));
	}
	
	// make the next status clickable to activate
	$step_action = 'status_opened';
	$status_steps[($media['SanitizeStatus']['order']-1)]['title'] = $this->Html->link(
		$status_steps[($media['SanitizeStatus']['order']-1)]['title'], 
		array('action' => $step_action, $media['Media']['id']),
		array(
			'title' => __('Mark %s as %s', __('Media'), $status_steps[($media['SanitizeStatus']['order']-1)]['title']),
			'confirm' => __('Are you sure you want to do this?'),
		)
	);
}
///// SIGNED
elseif($media['SanitizeStatus']['order'] == 3)
{
	if(AuthComponent::user('signer'))
	{
		$page_options[] = $this->Html->link(__('Re-Sign'), array('action' => 'status_sign', $media['Media']['id']));
	}
	
	if(in_array(AuthComponent::user('role'), array('admin', 'reviewer')))
	{
		// make the next status clickable to activate
		$step_action = 'status_released';
		$status_steps[($media['SanitizeStatus']['order']+1)]['title'] = $this->Html->link(
			$status_steps[($media['SanitizeStatus']['order']+1)]['title'], 
			array('action' => $step_action, $media['Media']['id']),
			array(
				'title' => __('Mark %s as %s', __('Media'), $status_steps[($media['SanitizeStatus']['order']+1)]['title']),
				'confirm' => __('Are you sure you want to do this?'),
			)
		);
		$page_options[] = $this->Html->link(__('Release Media'), array('action' => $step_action, $media['Media']['id']));
	}
}
///// verified, allowed to be released for descruction
elseif($media['SanitizeStatus']['order'] == 4)
{
}


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
	$page_options[] = $this->Html->link(__('Download Signed PDF Form'), array('action' => 'download', $media['Media']['id'], 0, $media['Media']['form_filename']));
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

if(in_array(AuthComponent::user('role'), array('admin')))
{
	$page_options_admin[] = $this->Html->link(__('Revert Status'), array('action' => 'change_status', $media['Media']['id'], 'admin' => true));
	$page_options_admin[] = $this->Html->link(__('Mark Tested'), array('action' => 'mark_tested', $media['Media']['id'], 'admin' => true));
	$page_options_admin[] = $this->Html->link(__('Delete'), array('action' => 'delete', $media['Media']['id'], 'admin' => true), array('confirm' => Configure::read('Dialogues.deletemedia')));
}

//////////////////




$tabs[] = array(
	'key' => 'description',
	'title' => __('Notes'),
	'content' => $this->Wrap->descView($media['Media']['details']),
);


if($media['MediaRepurposedUser']['id'])
{
	$stats[] = array(
		'id' => 'MediaLogRepurposed',
		'name' => __('Repurposed Histroy'), 
		'ajax_count_url' => array('controller' => 'media_logs', 'action' => 'media_repurposed', $media['Media']['id']),
		'tab' => array('tabs', 1), // the tab to display
	);
	$tabs[] = array(
		'key' => 'MediaLogRepurposed',
		'title' => __('Repurposed Histroy'),
		'url' => array('controller' => 'media_logs', 'action' => 'media_repurposed', $media['Media']['id']),
	);
}

//if(in_array(AuthComponent::user('role'), array('admin', 'reviewer')))
if(in_array(AuthComponent::user('role'), array('admin')))
{
	$stats[] = array(
		'id' => 'MediaLog',
		'name' => __('Media Histroy'), 
		'ajax_count_url' => array('controller' => 'media_logs', 'action' => 'media', $media['Media']['id'], 'admin' => true),
		'tab' => array('tabs', 2), // the tab to display
	);
	$tabs[] = array(
		'key' => 'MediaLog',
		'title' => __('Media Histroy'),
		'url' => array('controller' => 'media_logs', 'action' => 'media', $media['Media']['id'], 'admin' => true),
	);
	$tabs[] = array(
		'key' => 'mediaLogDetails',
		'title' => __('Media Log Details'),
		'content' => ' ',
	);
}

$stats[] = array(
	'id' => 'tagsReport',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'media', $media['Media']['id']),
	'tab' => array('tabs', 2), // the tab to display
);	
$tabs[] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'media', $media['Media']['id']),
);

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('%s Details', __('Media')),
	'page_options' => $page_options,
	'page_options2' => $page_options_admin,
	'details_left_title' => ' ',
	'details_left' => $details_left,
	'details_right_title' => ' ',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
	'status_steps' => $status_steps,
));