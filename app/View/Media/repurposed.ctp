<?php 
// File: app/View/Media/repurposed.ctp


$page_options = array(
//	$this->Html->link(__('Add %s', __('Media')), array('action' => 'add')),
);

// content
$th = array(
	
	'Media.id' => array('content' => __('ID'), 'options' => array('sort' => 'Media.id')),
	'MediaLog.serial_number' => array('content' => __('Serial Number'), 'options' => array('sort' => 'MediaLog.serial_number')),
	'MediaLog.asset_tag' => array('content' => __('Asset Tag'), 'options' => array('sort' => 'MediaLog.asset_tag')),
	'MediaLog.example_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'MediaLog.example_ticket')),
	'RepurposeStatus.name' => array('content' => __('%s Status', __('Repurpose')), 'options' => array('sort' => 'RepurposeStatus.name')),
	'Media.repurposed_notes' => array('content' => __('Repurposed Notes')),
	'MediaRepurposedUser.name' => array('content' => __('Last Repurposed By'), 'options' => array('sort' => 'MediaRepurposedUser.name')),
	'Media.repurposed_date' => array('content' => __('Repurposed On'), 'options' => array('sort' => 'Media.repurposed_date')),
	'MediaType.name' => array('content' => __('Media Type'), 'options' => array('sort' => 'MediaType.name')),
	'Media.size' => array('content' => __('Size'), 'options' => array('sort' => 'Media.size')),
	'Media.manufacturer' => array('content' => __('Manufacturer'), 'options' => array('sort' => 'Media.manufacturer')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($_media as $i => $media)
{
	$actions = $this->Html->link(__('View'), array('action' => 'view', $media['Media']['id']));
	if(in_array(AuthComponent::user('role'), array('admin')))
	{
		$actions .= $this->Html->link(__('Remove from Repurposed'), array('action' => 'unrepurpose', $media['Media']['id'], 'admin' => true));
	}
	
	$MediaRepurposedUser = '';
	if($media['MediaRepurposedUser']['id'])
	{
		$tmp = array('User' => $media['MediaRepurposedUser']);
		$MediaRepurposedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$this->Html->link($media['Media']['serial_number'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$this->Html->link($media['Media']['asset_tag'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$this->Html->link($media['Media']['example_ticket'], array('action' => 'mini_view', $media['Media']['id']), array('class' => 'mini-view')),
		$media['RepurposeStatus']['name'],
		$media['Media']['repurposed_notes'],
		$MediaRepurposedUser,
		$this->Wrap->niceTime($media['Media']['repurposed_date']),
		$media['MediaType']['name'],
		$media['Media']['size'],
		$media['Media']['manufacturer'],
		array(
			$actions, 
			array('class' => 'actions'),
		),
	);
}

$subtitle = __('All');
if(isset($this->passedArgs[0]))
{
	$subtitle = $this->Local->repurposeStatus($this->passedArgs[0]);
}


echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s: %s', __('Repurposed'), __('Media'), $subtitle),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));