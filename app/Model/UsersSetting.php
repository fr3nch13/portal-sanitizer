<?php
App::uses('AppModel', 'Model');
/**
 * UserSetting Model
 *
 * @property User $User
 */
class UsersSetting extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'email_new' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'email_updated' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'email_closed' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);
}
