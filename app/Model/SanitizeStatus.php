<?php
App::uses('AppModel', 'Model');

class SanitizeStatus extends AppModel 
{
	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => 'create',
				'message' => 'Enter a Name.',
			),
		),
		'threshold' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Must be a number.',
			),
			'gtZero' => array(
				'rule' => array('comparison', '>=', 1),
				'allowEmpty' => false,
				'message' => 'Must be greater than 0.',
			),
		),
	);
	
	public $hasMany = array(
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'sanitize_status_id',
			'dependent' => false,
		),
		'MediaLog' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'sanitize_status_id',
			'dependent' => false,
		),
	);
	
	public $actsAs = array(
		'Utilities.Email',
		'Utilities.Shell',
	);
	
	public $toggleFields = array('active', 'sendemail');
	
	public function statuses($use_id = false, $limit = false)
	{
		$conditions = array(
			'active' => true,
			'order >' => 0,
		);
		if($limit !== false)
		{
			$conditions['order <='] = (int)$limit;
		}
		
		$fields = array('order', 'name');
		if($use_id)
			$fields[0] = 'id';
		
		return $this->find('list', array(
			'recursive' => -1,
 			'fields' => $fields,
			'order' => array('order' => 'asc'),
			'conditions' => $conditions,
		));
	}
	
	public function statusName($status_id = false)
	{
		$this->id = $status_id;
		return $this->field('name');
	}
	
	public function idFromOrder($order = false)
	{
		return $this->field('id', array('order' => $order));
	}
	
	public function orderFromId($id = false)
	{
		return $this->field('order', array('id' => $id));
	}
	
	public function changeOrders($data = false)
	{
		if(!$data)
			return false;
		if(!is_array($data))
			return false;
		
		// first mark all of them as 0
		$this->updateAll(
			array($this->alias.'.order' => 0), 
			array($this->alias.'.order >' => 0)
		);

		$saveData = array();
		foreach($data as $id => $order)
		{
			if(preg_match('/_/', $id))
				list($model, $field, $id) = explode('_', $id);
			
			$saveData[$id] = array('id' => $id, 'order' => $order);
		}
		return $this->saveMany($saveData);
	}
	
	public function cron_sanitize_status_reminders()
	{
		// the current hour
		list($hour, $day) = explode('-', date('G-D'));
		$day = strtolower($day);
		
		// sanitize statuses that have been setup to email today at this time
		$conditions = array(
			'SanitizeStatus.sendemail' => true,
			'SanitizeStatus.'.$day => true,
			'SanitizeStatus.notify_time' => $hour,
			'SanitizeStatus.notify_email !=' => '',
		);
		
		$sanitize_statuses = $this->find('all', array(
			'conditions' => $conditions,
		));
		
		// send a batch email for each of the sanitize statuses
		foreach($sanitize_statuses as $sanitize_status)
		{
			// get a list of media for this status
			$media = $this->Media->find('all', array(
				'recursive' => 0,
				'conditions' => array(
					'Media.sanitize_status_id' => $sanitize_status['SanitizeStatus']['id'],
					'Media.repurpose_status_id' => 0,
				),
			));
			
			if(!$media)
				continue;
			
			$media_count = count($media);
			// make sure we have met the threshold for this status
			if($media_count < $sanitize_status['SanitizeStatus']['threshold'])
			{
				$this->shellOut(__('There are only %s %s assigned to this %s. It requires at least %s. No email will be sent', $media_count, __('Media'), __('Status'), $sanitize_status['SanitizeStatus']['threshold'] ));
				continue;
			}
			
			/// setup the email
			$subject = __('There are %s %s items in the %s: %s', $media_count, __('Media'), __('Status'), $sanitize_status['SanitizeStatus']['name']);
			$this->shellOut($subject);
	 		
	 		$viewVars = array(
	 			'subject' => $subject,
	 			'sanitize_status' => $sanitize_status,
	 			'_media' => $media,
	 		);
	 		
	 		$this->Email_reset();
			$this->Email_set('to', preg_split("/,\s*/", $sanitize_status['SanitizeStatus']['notify_email']));
			$this->Email_set('subject', $subject);
			$this->Email_set('emailFormat', 'text');
			$this->Email_set('template', 'cron_sanitize_status_reminders');
			$this->Email_set('viewVars', $viewVars);
			
			// send the email
//			$this->Email_set('debug', true);
			if(!$results = $this->Email_executeFull())
			{
				$this->shellOut(__('Error sending notification email for %s "%s".', __('Sanitize Status'), $sanitize_status['SanitizeStatus']['name']));
			}
			
			$this->shellOut(__('Sent notification email for %s "%s".', __('Sanitize Status'), $sanitize_status['SanitizeStatus']['name']));
		}
	}
}
