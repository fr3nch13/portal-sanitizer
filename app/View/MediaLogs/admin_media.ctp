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
	'SanitizeStatus.name' => array('content' => __('%s Status', __('Sanitize')), 'options' => array('sort' => 'SanitizeStatus.name')),
	'RepurposeStatus.name' => array('content' => __('%s Status', __('Repurpose')), 'options' => array('sort' => 'RepurposeStatus.name')),
	'MediaLogModifiedUser.name' => array('content' => __('Changed By'), 'options' => array('sort' => 'MediaLogModifiedUser.name')),
	'MediaLog.modified' => array('content' => __('Changed On'), 'options' => array('sort' => 'MediaLog.modified')),
	'MediaLog.log_created' => array('content' => __('Log Created'), 'options' => array('sort' => 'MediaLog.log_created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($media_logs as $i => $media_log)
{
	$MediaLogModifiedUser = '&nbsp;';
	if(isset($media_log['MediaLogModifiedUser']['name']))
	{
		$tmp = array('User' => $media_log['MediaLogModifiedUser']);
		$MediaLogModifiedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $media_log['MediaLog']['id']), array('class' => 'tabload'));
	
	$td[$i] = array(
		$this->Html->link($media_log['MediaLog']['id'], array('action' => 'view', $media_log['MediaLog']['id']), array('class' => 'tabload')),
		$this->Html->link($media_log['MediaLog']['serial_number'], array('action' => 'view', $media_log['MediaLog']['id']), array('class' => 'tabload')),
		$media_log['MediaLog']['asset_tag'],
		$this->Html->link($media_log['MediaLog']['example_ticket'], array('action' => 'view', $media_log['MediaLog']['id']), array('class' => 'tabload')),
		$media_log['SanitizeStatus']['name'],
		$media_log['RepurposeStatus']['name'],
		$MediaLogModifiedUser,
		$this->Wrap->niceTime($media_log['MediaLog']['modified']),
		$this->Wrap->niceTime($media_log['MediaLog']['log_created']),
		array(
			$actions, 
			array('class' => 'actions'),
		),
		'multiselect' => $media_log['MediaLog']['id'],
		
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All %s', __('MediaLog Logs')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
?>

<script type="text/javascript">

var submitResults = false;

$(document).ready(function()
{
	var details_index = $('#tabs a[href="#tabs-mediaLogDetails"]').parent().index();
	
	$('a.tabload').bind("click", function (event) {
		if($("#tabs-mediaLogDetails").load($(this).attr('href')))
		{
			$('#tabs').tabs('select', details_index);
		}
		return false;
	});
});//ready 

</script>