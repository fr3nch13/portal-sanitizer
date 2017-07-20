<?php
App::uses('AppModel', 'Model');

class TestStatus extends AppModel 
{
	public $displayField = 'name';
	
	public $hasMany = array(
		'MediaLog' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'test_status_id',
		),
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'test_status_id',
		),
	);
}
