<?php 
// File: app/View/MediaTypes/index.ctp


$page_options = array(
	$this->Html->link(__('Add Media Type'), array('action' => 'add')),
);

// content
$th = array(
	'MediaType.name' => array('content' => __('Media Type'), 'options' => array('sort' => 'MediaType.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($media_types as $i => $media_type)
{
	$td[$i] = array(
		$media_type['MediaType']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $media_type['MediaType']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $media_type['MediaType']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Media Types'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));