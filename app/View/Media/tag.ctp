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
	'Media.state' => array('content' => __('State'), 'options' => array('sort' => 'Media.state')),
	'Media.signed' => array('content' => __('Signed'), 'options' => array('sort' => 'Media.signed')),
	'Media.tested' => array('content' => __('Tested'), 'options' => array('sort' => 'Media.tested')),
	'MediaModifiedUser.name' => array('content' => __('Last Updated By'), 'options' => array('sort' => 'MediaModifiedUser.name')),
	'MediaAddedUser.name' => array('content' => __('Media Added By'), 'options' => array('sort' => 'MediaAddedUser.name')),
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
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $media['Media']['id']));
	
	if(in_array(AuthComponent::user('role'), array('admin', 'reviewer', 'manager')))
	{
		$actions .= $this->Html->link(__('Edit'), array('action' => 'edit', $media['Media']['id'], $this->params['prefix'] => false));
	}
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'view', $media['Media']['id'])),
		$this->Html->link($media['Media']['serial_number'], array('action' => 'view', $media['Media']['id'])),
		$media['Media']['asset_tag'],
		$this->Html->link($media['Media']['example_ticket'], array('action' => 'view', $media['Media']['id'])),
		$media['SanitizeStatus']['name'],
		$this->Local->state($media['Media']['state']),
		$this->Local->signed($media['Media']['signed']),
		$this->Local->tested($media['Media']['tested']),
		$MediaModifiedUser,
		$MediaAddedUser,
		$this->Wrap->niceTime($media['Media']['created']),
		array(
			$actions, 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Tagged %s', __('Media')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));