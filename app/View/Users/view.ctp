<?php 
// File: app/View/Users/view.ctp
$page_options = array(
);

$details = array(
	array('name' => __('Email'), 'value' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email'])),
	array('name' => __('AD Account'), 'value' => $user['User']['adaccount']),
);

$stats = array(
	array(
		'id' => 'AllMedia',
		'name' => __('Associated %s', __('Media')), 
		'tip' => __('All %s they are involved with.', __('Media')),
		'ajax_count_url' => array('controller' => 'media', 'action' => 'user', $user['User']['id']),
		'tab' => array('tabs', '1'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'Media',
		'title' => __('Associated Media'),
		'url' => array('controller' => 'media', 'action' => 'user', $user['User']['id']),
	),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('User'), $user['User']['name']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs' => $tabs,
));