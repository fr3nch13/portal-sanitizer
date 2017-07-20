<?php 
// File: app/View/RepurposeStatuses/index.ctp


$page_options = array();

if(in_array(AuthComponent::user('role'), array('admin')))
{
	$page_options[] = $this->Html->link(__('Add %s Status', __('Repurpose')), array('action' => 'add', 'admin' => true));
}
// content
$th = array(
	'RepurposeStatus.name' => array('content' => __('%s Status', __('Repurpose'))),
	'RepurposeStatus.active' => array('content' => __('Active')),
	'RepurposeStatus.sendemail' => array('content' => __('Send Email?'), 'options' => array('sort' => 'RepurposeStatus.sendemail')),
	'RepurposeStatus.threshold' => array('content' => __('Threshold'), 'options' => array('sort' => 'RepurposeStatus.threshold')),
	'RepurposeStatus.notify_email' => array('content' => __('Notification Email'), 'options' => array('sort' => 'RepurposeStatus.notify_email')),
	'RepurposeStatus.notify_time' => array('content' => __('Send Email At'), 'options' => array('sort' => 'RepurposeStatus.notify_time')),
	'RepurposeStatus.mon' => array('content' => __('Mon'), 'options' => array('sort' => 'RepurposeStatus.mon')),
	'RepurposeStatus.tue' => array('content' => __('Tues'), 'options' => array('sort' => 'RepurposeStatus.tue')),
	'RepurposeStatus.wed' => array('content' => __('Wed'), 'options' => array('sort' => 'RepurposeStatus.wed')),
	'RepurposeStatus.thu' => array('content' => __('Thurs'), 'options' => array('sort' => 'RepurposeStatus.thu')),
	'RepurposeStatus.fri' => array('content' => __('Fri'), 'options' => array('sort' => 'RepurposeStatus.fri')),
	'RepurposeStatus.sat' => array('content' => __('Sat'), 'options' => array('sort' => 'RepurposeStatus.sat')),
	'RepurposeStatus.sun' => array('content' => __('Sun'), 'options' => array('sort' => 'RepurposeStatus.sun')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($repurpose_statuses as $i => $repurpose_status)
{
	$active = $this->Wrap->yesNo($repurpose_status['RepurposeStatus']['active']);
	$sendemail = $this->Wrap->yesNo($repurpose_status['RepurposeStatus']['sendemail']);
	if(in_array(AuthComponent::user('role'), array('admin')))
	{
		$active = array(
			$this->Form->postLink($this->Wrap->yesNo($repurpose_status['RepurposeStatus']['active']), array('action' => 'toggle', 'active', $repurpose_status['RepurposeStatus']['id'], 'admin' => true),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
		$sendemail = array(
			$this->Form->postLink($this->Wrap->yesNo($repurpose_status['RepurposeStatus']['sendemail']), array('action' => 'toggle', 'sendemail', $repurpose_status['RepurposeStatus']['id'], 'admin' => true),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		);
	}
	
	$td[$i] = array(
		array(
			$repurpose_status['RepurposeStatus']['name'],
			array('id' => 'RepurposeStatis.id.'. $repurpose_status['RepurposeStatus']['id']),
		),
		$active,
		$sendemail,
		$repurpose_status['RepurposeStatus']['threshold'],
		$repurpose_status['RepurposeStatus']['notify_email'],
		$this->Local->emailTimes($repurpose_status['RepurposeStatus']['notify_time']),
		$this->Wrap->check($repurpose_status['RepurposeStatus']['mon']),
		$this->Wrap->check($repurpose_status['RepurposeStatus']['tue']),
		$this->Wrap->check($repurpose_status['RepurposeStatus']['wed']),
		$this->Wrap->check($repurpose_status['RepurposeStatus']['thu']),
		$this->Wrap->check($repurpose_status['RepurposeStatus']['fri']),
		$this->Wrap->check($repurpose_status['RepurposeStatus']['sat']),
		$this->Wrap->check($repurpose_status['RepurposeStatus']['sun']),
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $repurpose_status['RepurposeStatus']['id'])),
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
	'page_title' => __('%s Statuses', __('Repurpose')),
	'page_options' => $page_options,
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'sortable_options' => $sortable_options,
));