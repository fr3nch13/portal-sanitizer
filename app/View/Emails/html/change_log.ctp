<?php 
// File: app/View/Emails/html/change_log.ctp

$this->Html->setFull(true);

// content
$th = array(
	'object' => array('content' => __('Object')),
	'type' => array('content' => __('Type')),
	'timestamp' => array('content' => __('Timestamp')),
	'user' => array('content' => __('Generated By')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($logs as $log)
{
	
	$td[] = array(
		$log['model'],
		$log_status_map[$log['status']],
		$this->Wrap->niceTime($log['timestamp']),
		$this->Html->link($log['user'], array('controller' => 'users', 'action' => 'view', $log['user_id'])),
		array(
			$this->Html->link(__('View Media'), array('controller' => 'media', 'action' => 'view', $log['media_id'])),
			array('class' => 'actions'),
		),
	);
	$td[] = array(
		__('Changes Made:'),
		$log['message'],
	);
}


echo $this->element('Utilities.email_html_index', array(
	'page_title' => __('Changes Made'),
	'page_subtitle' => $subject,
	'page_description' => __('To change your email settings for this notification, please visit %s, and select the "Email Settings" tab.', $this->Html->link(__('This Link'), array('controller' => 'users', 'action' => 'edit'))),
	'th' => $th,
	'td' => $td,
));