<?php 
// File: app/View/MediaLog/admin_media.ctp


$page_options = array(
);

// content
$th = array(
	'MediaLog.id' => array('content' => __('ID'), 'options' => array('sort' => 'MediaLog.id')),
	'MediaLog.serial_number' => array('content' => __('Serial Number'), 'options' => array('sort' => 'MediaLog.serial_number')),
	'MediaLog.asset_tag' => array('content' => __('Asset Tag'), 'options' => array('sort' => 'MediaLog.asset_tag')),
	'MediaLog.example_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'MediaLog.example_ticket')),
	'RepurposeStatus.name' => array('content' => __('%s Status', __('Repurpose')), 'options' => array('sort' => 'RepurposeStatus.name')),
	'MediaLog.repurposed_notes' => array('content' => __('Notes')),
	'MediaLogRepurposedUser.name' => array('content' => __('Changed By'), 'options' => array('sort' => 'MediaLogRepurposedUser.name')),
	'MediaLog.repurposed_date' => array('content' => __('Changed'), 'options' => array('sort' => 'MediaLog.repurposed_date')),
);

$td = array();
foreach ($media_logs as $i => $media_log)
{
	$MediaLogRepurposedUser = '&nbsp;';
	if(isset($media_log['MediaLogRepurposedUser']['name']))
	{
		$tmp = array('User' => $media_log['MediaLogRepurposedUser']);
		$MediaLogRepurposedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$td[$i] = array(
		$media_log['MediaLog']['id'],
		$media_log['MediaLog']['serial_number'],
		$media_log['MediaLog']['asset_tag'],
		$media_log['MediaLog']['example_ticket'],
		$media_log['RepurposeStatus']['name'],
		$this->Wrap->descView($media_log['MediaLog']['repurposed_notes']),
		$MediaLogRepurposedUser,
		$this->Wrap->niceTime($media_log['MediaLog']['repurposed_date']),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All %s', __('MediaLog Logs')),
	'page_options' => $page_options,
	'search_placeholder' => __('Repurposed Logs'),
	'th' => $th,
	'td' => $td,
));