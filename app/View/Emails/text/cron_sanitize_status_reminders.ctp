<?php 
// File: app/View/Emails/text/cron_sanitize_status_reminders.ctp
$this->Html->setFull(true);
$this->Html->asText(true);

$page_options = array(
	$this->Html->link(__('View these %s', _('Media')), array('controller' => 'media', 'action' => 'index', $sanitize_status['SanitizeStatus']['order'])),
);

// content
$th = array(
	'Media.id' => array('content' => __('ID'), 'options' => array('sort' => 'Media.id')),
	'Media.serial_number' => array('content' => __('Serial Number'), 'options' => array('sort' => 'Media.serial_number')),
	'Media.asset_tag' => array('content' => __('Asset Tag'), 'options' => array('sort' => 'Media.asset_tag')),
	'Media.example_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'Media.example_ticket')),
	'Media.created' => array('content' => __('Created'), 'options' => array('sort' => 'Media.created')),
	'Media.modified' => array('content' => __('Last Updated'), 'options' => array('sort' => 'Media.modified')),
	'MediaModifiedUser.name' => array('content' => __('Last Updated By'), 'options' => array('sort' => 'MediaModifiedUser.name')),
	'MediaAddedUser.name' => array('content' => __('Media Added By'), 'options' => array('sort' => 'MediaAddedUser.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($_media as $i => $media)
{
	$MediaModifiedUser = '&nbsp;';
	if(isset($media['MediaModifiedUser']['name']))
	{
		$tmp = array('User' => $media['MediaModifiedUser']);
		$MediaModifiedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$tmp = array('User' => $media['MediaAddedUser']);
	$MediaAddedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $media['Media']['id']));
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'view', $media['Media']['id'])),
		$media['Media']['serial_number'],
		$media['Media']['asset_tag'],
		$media['Media']['example_ticket'],
		$this->Wrap->niceTime($media['Media']['created']),
		$this->Wrap->niceTime($media['Media']['modified']),
		$MediaModifiedUser,
		$MediaAddedUser,
		array(
			$actions, 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.email_text_index', array(
	'instructions' => $sanitize_status['SanitizeStatus']['instructions'],
	'page_title' => __('%s %s', $sanitize_status['SanitizeStatus']['name'], __('Media')),
	'page_subtitle' => $subject,
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));