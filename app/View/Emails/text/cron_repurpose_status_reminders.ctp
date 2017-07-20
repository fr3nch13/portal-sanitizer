<?php 
// File: app/View/Emails/text/cron_repurpose_status_reminders.ctp
$this->Html->setFull(true);
$this->Html->asText(true);

$page_options = array(
	$this->Html->link(__('View these %s', _('Media')), array('controller' => 'media', 'action' => 'repurposed', $repurpose_status['RepurposeStatus']['order'])),
);

// content
$th = array(
	'Media.id' => array('content' => __('ID'), 'options' => array('sort' => 'Media.id')),
	'Media.serial_number' => array('content' => __('Serial Number'), 'options' => array('sort' => 'Media.serial_number')),
	'Media.asset_tag' => array('content' => __('Asset Tag'), 'options' => array('sort' => 'Media.asset_tag')),
	'Media.example_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'Media.example_ticket')),
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
	$MediaRepurposedUser = '';
	if($media['MediaRepurposedUser']['id'])
	{
		$tmp = array('User' => $media['MediaRepurposedUser']);
		$MediaRepurposedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']));  
	}
	
	$MediaModifiedUser = '&nbsp;';
	if(isset($media['MediaModifiedUser']['name']))
	{
		$tmp = array('User' => $media['MediaModifiedUser']);
		$MediaModifiedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']));  
	}
	
	$tmp = array('User' => $media['MediaAddedUser']);
	$MediaAddedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']));  
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $media['Media']['id']));
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('controller' => 'media', 'action' => 'view', $media['Media']['id'])),
		$media['Media']['serial_number'],
		$media['Media']['asset_tag'],
		$media['Media']['example_ticket'],
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

echo $this->element('Utilities.email_text_index', array(
	'instructions' => $repurpose_status['RepurposeStatus']['instructions'],
	'page_title' => __('%s %s', $repurpose_status['RepurposeStatus']['name'], __('Media')),
	'page_subtitle' => $subject,
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));