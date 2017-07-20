<?php
// app/Model/User.php

App::uses('AppModel', 'Model');

class User extends AppModel
{
	public $name = 'User';
	
	public $displayField = 'name';
	
	public $validate = array(
		'email' => array(
			'required' => array(
				'rule' => array('email'),
				'message' => 'A valid email adress is required',
			)
		),
		'role' => array(
			'valid' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false,
			),
		),
	);
	
	public $hasOne = array(
		'UsersSetting' => array(
			'className' => 'UsersSetting',
			'foreignKey' => 'user_id',
		)
	);
	
	public $hasMany = array(
		'LoginHistory' => array(
			'className' => 'LoginHistory',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'MediaAddedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'added_user_id',
			'dependent' => false,
		),
		'MediaModifiedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'modified_user_id',
			'dependent' => false,
		),
		'MediaSanitizedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'sanitized_user_id',
			'dependent' => false,
		),
		'MediaOpenedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'opened_user_id',
			'dependent' => false,
		),
		'MediaSignedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'signed_user_id',
			'dependent' => false,
		),
		'MediaTestedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'tested_user_id',
			'dependent' => false,
		),
		'MediaRepurposedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'repurposed_user_id',
			'dependent' => false,
		),
		'MediaLogAddedUser' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'added_user_id',
			'dependent' => false,
		),
		'MediaLogModifiedUser' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'modified_user_id',
			'dependent' => false,
		),
		'MediaLogClosedUser' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'closed_user_id',
			'dependent' => false,
		),
		'MediaLogOpenedUser' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'opened_user_id',
			'dependent' => false,
		),
		'MediaLogSignedUser' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'signed_user_id',
			'dependent' => false,
		),
		'MediaLogTestedUser' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'tested_user_id',
			'dependent' => false,
		),
		'MediaLogRepurposedUser' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'repurposed_user_id',
			'dependent' => false,
		),
	);
	
	public $actsAs = array(
		'Snapshot.Stat' => array(
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
				'active' => array(
					'conditions' => array(
						'User.active' => true,
					),
				),
			),
		),
    );
	
	// define the fields that can be searched
	public $searchFields = array(
		'User.name',
		'User.email',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active', 'signer');
	
	// the path to the config file.
	public $configPath = false;
	
	// Any error relating to the config
	public $configError = false;
	
	// used to store info, because the photo name is changed.
	public $afterdata = false;
	
	public function beforeSave($options = array())
	{
		// hash the password before saving it to the database
		if (isset($this->data[$this->alias]['password']))
		{
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		// if we edited ourselves
		if($this->id == AuthComponent::user('id'))
		{
			$user = $this->findById(AuthComponent::user('id'));
			CakeSession::write('Auth',$user);
		}
	}
	
	public function lastLogin($user_id = null)
	{
		if($user_id)
		{
			$this->id = $user_id;
			// callback false to aviod reupdating the session that was just created
			return $this->saveField('lastlogin', date('Y-m-d H:i:s'), array('callbacks' => false));
		}
		return false;
	}
	
	public function loginAttempt($input = false, $success = false, $user_id = false)
	{
		$email = false;
		if(isset($input['User']['email'])) 
		{
			$email = $input['User']['email'];
			if(!$user_id)
			{
				$user_id = $this->field('id', array('email' => $email));
			}
		}
		
		$data = array(
			'email' => $email,
			'user_agent' => env('HTTP_USER_AGENT'),
			'ipaddress' => env('REMOTE_ADDR'),
			'success' => $success,
			'user_id' => $user_id,
			'timestamp' => date('Y-m-d H:i:s'),
		);
		
		$this->LoginHistory->create();
		return $this->LoginHistory->save($data);
	}
	
	
	public function adminEmails()
	{
		return $this->emails('admin', true);
	}
	
	public function reviewerEmails()
	{
		return $this->emails('reviewer', true);
	}
	
	public function signerEmails()
	{
		$conditions = array(
			'active' => $active,
			'signer' => true,
		);
		
		return $this->find('list', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'fields' => array(
				'email',
			),
		));
	}
	
	public function emails($role = false, $active = true)
	{
		$conditions = array(
			'active' => $active,
		);
		
		if($role)
		{
			$conditions['role'] = $role;
		}
		
		return $this->find('list', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'fields' => array(
				'email',
			),
		));
	}
	
	public function userList($user_ids = array(), $recursive = 0)
	{
		// fill the user cache
		$_users = $this->find('all', array(
			'recursive' => $recursive,
			'conditions' => array(
				'User.id' => $user_ids,
			),
		));
		
		$users = array();
		
		foreach($_users as $user)
		{
			$user_id = $user['User']['id'];
			$users[$user_id] = $user; 
		}
		
		unset($_users);
		return $users;
	}
	
	public function changeLogList($user_ids = array(), $recursive = 0)
	{
		// fill the user cache
		$_users = $this->find('all', array(
			'recursive' => $recursive,
			'conditions' => array(
				'or' => array(
					'User.id' => $user_ids,
					'UsersSetting.email_new' => 2,
					'UsersSetting.email_updated' => 2,
					'UsersSetting.email_closed' => 2,
				),
			),
		));

		$users = array();
		
		foreach($_users as $user)
		{
			$user_id = $user['User']['id'];
			$users[$user_id] = $user;
		}
		
		unset($_users);
		return $users;
	}
}

