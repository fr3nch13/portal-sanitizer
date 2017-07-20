<?php

$max_upload = (int)(ini_get('upload_max_filesize'));
$max_post = (int)(ini_get('post_max_size'));
$memory_limit = (int)(ini_get('memory_limit'));
$upload_mb = min($max_upload, $max_post, $memory_limit);

// content
$th = array(
	'Media.id' => array('content' => __('ID'), 'options' => array('sort' => 'Media.id')),
	'Media.serial_number' => array('content' => __('Serial Number'), 'options' => array('sort' => 'Media.serial_number')),
	'Media.asset_tag' => array('content' => __('Asset Tag'), 'options' => array('sort' => 'Media.asset_tag')),
	'Media.example_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'Media.example_ticket')),
	'Media.download' => array('content' => __('Download PDF Form')),
	'Media.upload' => array('content' => __('Upload Signed PDF Form')),
);

$td = array();
foreach ($_media as $i => $media)
{
	$actions = $this->Form->input($media['Media']['id'].'.Media.id', array('type' => 'hidden', 'value' => $media['Media']['id']));
	$actions .= $this->Form->input($media['Media']['id'].'.Media.serial_number', array('type' => 'hidden', 'value' => $media['Media']['serial_number']));
	$actions .= $this->Form->input($media['Media']['id'].'.Media.file', array(
		'type' => 'file',
		'div' => false,
		'label' => false,
	));
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'view', $media['Media']['id'])),
		$this->Html->link($media['Media']['serial_number'], array('action' => 'view', $media['Media']['id'])),
		$media['Media']['asset_tag'],
		$this->Html->link($media['Media']['example_ticket'], array('action' => 'view', $media['Media']['id'])),
		array( 
			$this->Html->link(__('Download Form'), array('action' => 'get_form', $media['Media']['id']), array('class' => 'no-icon')),
			array('class' => 'actions'),
		),
		array( 
			$actions,
			array('class' => 'actions'),
		),
		
	);
}


if($td)
{
	$before_table = $this->Form->create('Media', array('type' => 'file'));
	$after_table = $this->Form->end(__('Sign %s', __('Media')));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s multiple %s', __('Sign'), __('Media')),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
));