<?php 
// File: app/View/Media/basic_index.ctp


$page_options = array(
//	$this->Html->link(__('Add %s', __('Media')), array('action' => 'add')),
);

// content
$th = array(
	'Media.serial_number' => array('content' => __('Serial Number'), 'options' => array('sort' => 'Media.serial_number')),
	'SanitizeStatus.name' => array('content' => __('%s Status', __('Sanitize')), 'options' => array('sort' => 'SanitizeStatus.name')),
	'MediaType.name' => array('content' => __('Media Type'), 'options' => array('sort' => 'MediaType.name')),
	'MediaModifiedUser.name' => array('content' => __('Last Updated By'), 'options' => array('sort' => 'MediaModifiedUser.name')),
//	'Media.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'Media.modified')),
	'MediaAddedUser.name' => array('content' => __('Media Added By'), 'options' => array('sort' => 'MediaAddedUser.name')),
//	'Media.created' => array('content' => __('Created'), 'options' => array('sort' => 'Media.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($_media as $i => $media)
{	
	$td[$i] = array(
		$media['Media']['serial_number'],
		$media['SanitizeStatus']['name'],
		$media['MediaType']['name'],
		$media['MediaModifiedUser']['name'],
//		$this->Wrap->niceTime($media['Media']['modified']),
		$media['MediaAddedUser']['name'],
//		$this->Wrap->niceTime($media['Media']['created']),
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $media['Media']['id'])), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Open %s', __('Media')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));