<?php 
// File: app/View/Users/index.ctp

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email')),
	'User.adaccount' => array('content' => __('AD Account'), 'options' => array('sort' => 'User.adaccount')),
	'User.active' => array('content' => __('Active'), 'options' => array('sort' => 'User.active')),
	'User.signer' => array('content' => __('ISSO Reviewer'), 'options' => array('sort' => 'User.signer')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($users as $i => $user)
{
	$tmp = array('User' => $user['User']);
	$User = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  

	$td[$i] = array(
		$this->Html->link($user['User']['name'], array('controller' => 'users', 'action' => 'view', $user['User']['id'])),
		$this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email']),
		$user['User']['adaccount'],
		$this->Wrap->yesNo($user['User']['active']),
		$this->Wrap->yesNo($user['User']['signer']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Users'),
	'th' => $th,
	'td' => $td,
));