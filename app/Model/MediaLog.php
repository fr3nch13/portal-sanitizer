<?php
App::uses('AppModel', 'Model');

class MediaLog extends AppModel 
{
	public $belongsTo = array(
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'media_id',
		),
		'SanitizeStatus' => array(
			'className' => 'SanitizeStatus',
			'foreignKey' => 'sanitize_status_id',
		),
		'RepurposeStatus' => array(
			'className' => 'RepurposeStatus',
			'foreignKey' => 'repurpose_status_id',
		),
		'MediaLogAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'MediaLogModifiedUser' => array(
			'className' => 'User',
			'foreignKey' => 'modified_user_id',
		),
		'MediaLogSanitizedUser' => array(
			'className' => 'User',
			'foreignKey' => 'sanitized_user_id',
		),
		'MediaLogOpenedUser' => array(
			'className' => 'User',
			'foreignKey' => 'opened_user_id',
		),
		'MediaLogSignedUser' => array(
			'className' => 'User',
			'foreignKey' => 'signed_user_id',
		),
		'MediaLogTestedUser' => array(
			'className' => 'User',
			'foreignKey' => 'tested_user_id',
		),
		'MediaLogRepurposedUser' => array(
			'className' => 'User',
			'foreignKey' => 'repurposed_user_id',
		),
		'MediaType' => array(
			'className' => 'MediaType',
			'foreignKey' => 'media_type_id',
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Email',
	);
	
	// to hide this model from the tags details page
	public $tags_hidden = true;
	
	// define the fields that can be searched
	public $searchFields = array(
		'RepurposeStatus.name',
		'MediaLogRepurposedUser.name',
		'MediaLogRepurposedUser.email',
	);
}