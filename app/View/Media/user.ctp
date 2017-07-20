<?php 
// File: app/View/Media/index.ctp


$page_options = array(
//	$this->Html->link(__('Add %s', __('Media')), array('action' => 'add')),
);

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
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
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
		$actions .= $this->Html->link(__('Download Form'), array('action' => 'download', $media['Media']['id'], 0, $media['Media']['form_filename']), array('class' => 'no-icon'));
		
		$tmp = array('User' => $media['MediaSignedUser']);
		$MediaStatusUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	if($media['SanitizeStatus']['order'] == 4)
	{
		$tmp = array('User' => $media['MediaReleasedUser']);
		$MediaStatusUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'view', $media['Media']['id'])),
		$this->Html->link($media['Media']['serial_number'], array('action' => 'view', $media['Media']['id'])),
		$this->Html->link($media['Media']['asset_tag'], array('action' => 'view', $media['Media']['id'])),
		$this->Html->link($media['Media']['example_ticket'], array('action' => 'view', $media['Media']['id'])),
		$media['SanitizeStatus']['name'],
		$MediaStatusUser,
		$MediaModifiedUser,
		$MediaAddedUser,
		$media['TestStatus']['name'],
		$this->Wrap->niceTime($media['Media']['created']),
		array(
			$actions, 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Associated %s', __('Media')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));