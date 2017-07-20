<?php 
// File: app/View/Media/index.ctp

$page_options = array(
//	$this->Html->link(__('Add %s', __('Media')), array('action' => 'add')),
);

/// multiselect
$use_multiselect = false;
$multiselect_options = array();
if(isset($this->passedArgs[0]) and in_array($this->passedArgs[0], array(1, 2, 3, 4)))
{
	$use_multiselect = true;
	if($this->passedArgs[0] == 1) // created, not sanitized
	{
		$multiselect_options['mark_sanitized'] = __('Mark as: %s', $this->Local->status(2));
	}
	elseif($this->passedArgs[0] == 2) // created, sanitized
	{
		$multiselect_options['mark_opened'] = __('Mark as: %s', $this->Local->status(1));
		
		if(AuthComponent::user('signer'))
		{
			$multiselect_options['multisign'] = __('Mark as: %s', $this->Local->status(3));
		}
	}
	elseif($this->passedArgs[0] == 3) // created, signed
	{
		$multiselect_options['batchdownload'] = __('Download all %s PDF Forms in one PDF.', __('Signed'));
		if(in_array(AuthComponent::user('role'), array('admin', 'reviewer', 'manager')))
		{
			$multiselect_options['mark_released'] = __('Mark as: %s', $this->Local->status(4));
		}
	}
	elseif($this->passedArgs[0] == 4) // released
	{
		$multiselect_options['batchdownload'] = __('Download all %s PDF Forms in one PDF.', __('Signed'));
	}
}

// content
$th = array(
	'Media.id' => array('content' => __('ID'), 'options' => array('sort' => 'Media.id')),
	'Media.serial_number' => array('content' => __('Serial Number'), 'options' => array('sort' => 'Media.serial_number')),
	'Media.asset_tag' => array('content' => __('Asset Tag'), 'options' => array('sort' => 'Media.asset_tag')),
	'Media.example_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'Media.example_ticket')),
	'SanitizeStatus.name' => array('content' => __('%s Status', __('Sanitize')), 'options' => array('sort' => 'SanitizeStatus.name')),
	'MediaStatusUser.name' => array('content' => __('Sanitize Status Set By')),
	'MediaModifiedUser.name' => array('content' => __('Last Updated By'), 'options' => array('sort' => 'MediaModifiedUser.name')),
	'MediaAddedUser.name' => array('content' => __('Media Added By'), 'options' => array('sort' => 'MediaAddedUser.name')),
	'SanitizeStatus.name' => array('content' => __('%s Status', __('Sanitize')), 'options' => array('sort' => 'SanitizeStatus.name')),
	'TestStatus.name' => array('content' => __('Test Status'), 'options' => array('sort' => 'TestStatus.name')),
	'Media.created' => array('content' => __('Created'), 'options' => array('sort' => 'Media.created')),
	'Media.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Media.modified')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => $use_multiselect,
);

$td = array();
foreach ($_media as $i => $media)
{
	$MediaModifiedUser = '&nbsp;';
	if(isset($media['MediaModifiedUser']['name']))
	{
		$tmp = array('User' => $media['MediaModifiedUser']);
		$MediaModifiedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$tmp = array('User' => $media['MediaAddedUser']);
	$MediaAddedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	
	// track who did the last state change
	$MediaStatusUser = false;
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $media['Media']['id']));
	//
	if($media['SanitizeStatus']['order'] == 1)
	{
		$actions .= $this->Html->link(__('Edit'), array('action' => 'edit', $media['Media']['id'], $this->params['prefix'] => false));
		$tmp = array('User' => $media['MediaOpenedUser']);
		$MediaStatusUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	elseif($media['SanitizeStatus']['order'] == 2)
	{
		$actions .= $this->Html->link(__('Edit'), array('action' => 'edit', $media['Media']['id'], $this->params['prefix'] => false));
		$actions .= $this->Html->link(__('Repurpose'), array('action' => 'mark_repurposed', $media['Media']['id'], $this->params['prefix'] => false));
		if(AuthComponent::user('signer'))
		{
			$actions .= $this->Html->link(__('Sign'), array('action' => 'status_sign', $media['Media']['id'], $this->params['prefix'] => false));
		}
		$tmp = array('User' => $media['MediaSanitizedUser']);
		$MediaStatusUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	elseif($media['SanitizeStatus']['order'] >= 3)
	{	
		if(AuthComponent::user('signer'))
		{
			$actions .= $this->Html->link(__('Re-Sign'), array('action' => 'status_sign', $media['Media']['id'], $this->params['prefix'] => false));
		}
		$actions .= $this->Html->link(__('Download Form'), array('action' => 'download', $media['Media']['id'], 0, $media['Media']['form_filename']), array('class' => 'no-icon'));
		
		$tmp = array('User' => $media['MediaSignedUser']);
		$MediaStatusUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	if($media['SanitizeStatus']['order'] == 4)
	{
		$tmp = array('User' => $media['MediaReleasedUser']);
		$MediaStatusUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	if(in_array(AuthComponent::user('role'), array('admin', 'reviewer', 'manager')))
	{
	}
	
	if(in_array(AuthComponent::user('role'), array('admin')))
	{
		$actions .= $this->Html->link(__('Delete'), array('action' => 'delete', $media['Media']['id'], 'admin' => true), array('confirm' => Configure::read('Dialogues.deletemedia')));
	}
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$this->Html->link($media['Media']['serial_number'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$this->Html->link($media['Media']['asset_tag'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$this->Html->link($media['Media']['example_ticket'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$media['SanitizeStatus']['name'],
		$MediaStatusUser,
		$MediaModifiedUser,
		$MediaAddedUser,
		$media['TestStatus']['name'],
		$this->Wrap->niceTime($media['Media']['created']),
		$this->Wrap->niceTime($media['Media']['modified']),
		array(
			$actions, 
			array('class' => 'actions'),
		),
		'multiselect' => $media['Media']['id'],
		
	);
}

$subtitle = __('All');
if(isset($this->passedArgs[0]))
{
	$subtitle = $this->Local->status($this->passedArgs[0]);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s', $subtitle, __('Media')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => $multiselect_options,
	'multiselect_referer' => array(
		'controller' => 'media',
		'action' => 'index',
		(isset($this->passedArgs[0])?$this->passedArgs[0]:false),
	)
));