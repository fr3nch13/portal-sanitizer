<?php 
// File: app/View/TestStatuses/admin_index.ctp


$page_options = array(
	$this->Html->link(__('Add %s', __('Test Status')), array('action' => 'add')),
);

// content
$th = array(
	'TestStatus.name' => array('content' => __('Test Status'), 'options' => array('sort' => 'TestStatus.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($test_statuses as $i => $test_status)
{
	$td[$i] = array(
		$test_status['TestStatus']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $test_status['TestStatus']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $test_status['TestStatus']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Test Statuses'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));