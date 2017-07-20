<?php 
// File: app/View/SanitizeStatuses/index.ctp


$page_options = array();

if(in_array(AuthComponent::user('role'), array('admin')))
{
	$page_options[] = $this->Html->link(__('Add %s Status', __('Sanitize')), array('action' => 'add', 'admin' => true));
}
// content
$th = array(
	'SanitizeStatus.name' => array('content' => __('%s Status', __('Sanitize'))),
	'SanitizeStatus.active' => array('content' => __('Active')),
	'SanitizeStatus.sendemail' => array('content' => __('Send Email?'), 'options' => array('sort' => 'SanitizeStatus.sendemail')),
	'SanitizeStatus.threshold' => array('content' => __('Threshold'), 'options' => array('sort' => 'SanitizeStatus.threshold')),
	'SanitizeStatus.notify_email' => array('content' => __('Notification Email'), 'options' => array('sort' => 'SanitizeStatus.notify_email')),
	'SanitizeStatus.notify_time' => array('content' => __('Send Email At'), 'options' => array('sort' => 'SanitizeStatus.notify_time')),
	'SanitizeStatus.mon' => array('content' => __('Mon'), 'options' => array('sort' => 'SanitizeStatus.mon')),
	'SanitizeStatus.tue' => array('content' => __('Tues'), 'options' => array('sort' => 'SanitizeStatus.tue')),
	'SanitizeStatus.wed' => array('content' => __('Wed'), 'options' => array('sort' => 'SanitizeStatus.wed')),
	'SanitizeStatus.thu' => array('content' => __('Thurs'), 'options' => array('sort' => 'SanitizeStatus.thu')),
	'SanitizeStatus.fri' => array('content' => __('Fri'), 'options' => array('sort' => 'SanitizeStatus.fri')),
	'SanitizeStatus.sat' => array('content' => __('Sat'), 'options' => array('sort' => 'SanitizeStatus.sat')),
	'SanitizeStatus.sun' => array('content' => __('Sun'), 'options' => array('sort' => 'SanitizeStatus.sun')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($sanitize_statuses as $i => $sanitize_status)
{
	$active = $this->Wrap->yesNo($sanitize_status['SanitizeStatus']['active']);
	$sendemail = $this->Wrap->yesNo($sanitize_status['SanitizeStatus']['sendemail']);
	if(in_array(AuthComponent::user('role'), array('admin')))
	{
		$active = array(
			$this->Form->postLink($this->Wrap->yesNo($sanitize_status['SanitizeStatus']['active']), array('action' => 'toggle', 'active', $sanitize_status['SanitizeStatus']['id'], 'admin' => true),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
		$sendemail = array(
			$this->Form->postLink($this->Wrap->yesNo($sanitize_status['SanitizeStatus']['sendemail']), array('action' => 'toggle', 'sendemail', $sanitize_status['SanitizeStatus']['id'], 'admin' => true),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
	}
	
	$td[$i] = array(
		array(
			$sanitize_status['SanitizeStatus']['name'],
			array('id' => 'SanitizeStatis.id.'. $sanitize_status['SanitizeStatus']['id']),
		),
		$active,
		$sendemail,
		$sanitize_status['SanitizeStatus']['threshold'],
		$sanitize_status['SanitizeStatus']['notify_email'],
		$this->Local->emailTimes($sanitize_status['SanitizeStatus']['notify_time']),
		$this->Wrap->check($sanitize_status['SanitizeStatus']['mon']),
		$this->Wrap->check($sanitize_status['SanitizeStatus']['tue']),
		$this->Wrap->check($sanitize_status['SanitizeStatus']['wed']),
		$this->Wrap->check($sanitize_status['SanitizeStatus']['thu']),
		$this->Wrap->check($sanitize_status['SanitizeStatus']['fri']),
		$this->Wrap->check($sanitize_status['SanitizeStatus']['sat']),
		$this->Wrap->check($sanitize_status['SanitizeStatus']['sun']),
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $sanitize_status['SanitizeStatus']['id'])),
			array('class' => 'actions'),
		),
	);
}

$sortable_options = false;

// only admins can reorder
if(in_array(AuthComponent::user('role'), array('admin')))
{
	$sortable_options = array(
		'enabled' => true,
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s Statuses', __('Sanitize')),
	'page_options' => $page_options,
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'sortable_options' => $sortable_options,
));