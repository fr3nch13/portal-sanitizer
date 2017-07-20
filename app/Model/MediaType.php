<?php
App::uses('AppModel', 'Model');

class MediaType extends AppModel 
{
	public $displayField = 'name';
	
	public $hasMany = array(
		'MediaLog' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'media_type_id',
		),
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'media_type_id',
		),
	);
}
