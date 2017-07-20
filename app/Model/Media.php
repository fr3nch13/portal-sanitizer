<?php
App::uses('AppModel', 'Model');

class Media extends AppModel 
{

	public $validate = array(
		'serial_number' => array(
			'isUnique' => array(
				'rule' => array('isUnique'),
				'required' => 'create',
				'message' => 'Already exists in the database.',
			),
		),
		'sanitize_status_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => 'create',
				'message' => 'Select a Sanitize Status',
			),
		),
	);
	
	public $belongsTo = array(
		'SanitizeStatus' => array(
			'className' => 'SanitizeStatus',
			'foreignKey' => 'sanitize_status_id',
			'plugin_snapshot' => true,
		),
		'TestStatus' => array(
			'className' => 'TestStatus',
			'foreignKey' => 'test_status_id',
			'plugin_snapshot' => true,
		),
		'RepurposeStatus' => array(
			'className' => 'RepurposeStatus',
			'foreignKey' => 'repurpose_status_id',
			'plugin_snapshot' => true,
		),
		'MediaAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'MediaModifiedUser' => array(
			'className' => 'User',
			'foreignKey' => 'modified_user_id',
		),
		'MediaOpenedUser' => array(
			'className' => 'User',
			'foreignKey' => 'opened_user_id',
		),
		'MediaSanitizedUser' => array(
			'className' => 'User',
			'foreignKey' => 'sanitized_user_id',
		),
		'MediaSignedUser' => array(
			'className' => 'User',
			'foreignKey' => 'signed_user_id',
		),
		'MediaReleasedUser' => array(
			'className' => 'User',
			'foreignKey' => 'released_user_id',
		),
		'MediaTestedUser' => array(
			'className' => 'User',
			'foreignKey' => 'tested_user_id',
		),
		'MediaRepurposedUser' => array(
			'className' => 'User',
			'foreignKey' => 'repurposed_user_id',
		),
		'MediaType' => array(
			'className' => 'MediaType',
			'foreignKey' => 'media_type_id',
			'plugin_snapshot' => true,
		),
	);
	
	public $hasMany = array(
		'MediaLog' => array(
			'className' => 'MediaLog',
			'foreignKey' => 'media_id',
			'dependent' => true,
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Email',
		'PhpPdf.PhpPdf' => array(),
		'Snapshot.Stat' => array(
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
			),
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Media.id',
		'Media.serial_number',
		'Media.asset_tag',
		'Media.example_ticket',
		'SanitizeStatus.name',
		'MediaAddedUser.name',
		'MediaAddedUser.email',
		'MediaModifiedUser.name',
		'MediaModifiedUser.email',
	);
	
	public $manageUploads = true;
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('state', 'signed');
	
	public $editedMedia = array();
	
	public $logBypass = false;
	
	public function beforeSave($options = array())
	{
		$updateSanitizedStatus = false;
		// this means we're editing an existing one, 
		if($this->id)
		{
			if(!$this->logBypass)
			{
				$this->data[$this->alias]['modified'] = date('Y-m-d H:i:s');
			}
			$newData = $this->data;
			$recursive = $this->recursive;
			$this->recursive = 0;
			$this->editedMedia = $this->read(null, $this->id);
			$this->data = $newData;
			$this->recursive = $recursive;
			
			// see if the sanitized status has changed.
			if(isset($this->data[$this->alias]['sanitize_status_id']) and isset($this->editedMedia[$this->alias]['sanitize_status_id']))
			{
				if($this->data[$this->alias]['sanitize_status_id'] != $this->editedMedia[$this->alias]['sanitize_status_id'])
					$updateSanitizedStatus = true;
			}
		}
		// being created
		else
		{
			$this->data[$this->alias]['opened_user_id'] = $this->data[$this->alias]['added_user_id'];
			$this->data[$this->alias]['opened_date'] = $this->data[$this->alias]['created'];
			
			if(isset($this->data[$this->alias]['sanitize_status_id']))
				$updateSanitizedStatus = true;
		}
			
		// see if it's in stage 2, if so, this person is also the sanitized user
		if(isset($this->data[$this->alias]['sanitize_status_id']) and $updateSanitizedStatus)
		{
			$order = $this->SanitizeStatus->orderFromId($this->data[$this->alias]['sanitize_status_id']);
			if($order == 2)
			{
				$this->data[$this->alias]['sanitized_user_id'] = AuthComponent::user('id');
				$this->data[$this->alias]['sanitized_date'] = date('Y-m-d H:i:s');
			}
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		/// save a copy in the media log table
		if($this->id and isset($this->editedMedia[$this->alias]) and !$this->logBypass)
		{
			$this->MediaLog->create();
			$this->MediaLog->data = array();
			$this->MediaLog->data['MediaLog'] = $this->editedMedia[$this->alias];
			$this->MediaLog->data['MediaLog']['media_id'] = $this->id;
			$this->MediaLog->data['MediaLog']['log_created'] = date('Y-m-d H:i:s');
			unset($this->MediaLog->data['MediaLog']['id']);
			
			$this->MediaLog->save($this->MediaLog->data);
		}
		
		return parent::afterSave($created, $options);
	}
	
	public function isOwnedBy($id, $user_id) 
	{
		return $this->field('id', array('id' => $id, 'added_user_id' => $user_id)) === $id;
	}
	
	public function sign($data = array())
	{
		// we're signing one, make it look like the multiple with one entry
		if(isset($data[$this->alias]))
		{
			$data = array(0 => $data);
		}
		
		$data_count = count($data);

		sort($data); // reset the keys
		if(isset($data[0][$this->alias]))
		{
			foreach($data as $i => $result)
			{
				if(!isset($result[$this->alias]['serial_number']))
				{
					$this->modelError = __('An internal error occurred, please let the administrator know (Media->sign)');
					return false;
				}
				$this->modelError = false;
				if(!$this->checkPdfFormSerial($result[$this->alias]['serial_number'], $result[$this->alias]['file']['tmp_name']))
				{
					if(!$this->modelError)
						$this->modelError = __('%s PDF Form%s doesn\'t match the record, please check the form.', ($data_count == 1?__('The'):__('One of the')), ($data_count == 1?'':'s') );
					return false;
				}
				
				if(!$signature = $this->PhpPdf_getSignature($result[$this->alias]['file']['tmp_name']))
				{
					$this->modelError = __('%s PDF Form%s isn\'t signed.', ($data_count == 1?__('The'):__('One of the')), ($data_count == 1?'':'s') );
					return false;
				}
				
				$data[$i][$this->alias]['signer_signature'] = $signature['name'];
				$data[$i][$this->alias]['signer_signed_date'] = $signature['date'];
				$data[$i][$this->alias]['signed_date'] = date('Y-m-d H:i:s');
			}
			return $this->saveMany($data);
		}
		$this->modelError = false;
		if(!$signature = $this->PhpPdf_getSignature($data[$this->alias]['file']['tmp_name']))
		{
			return false;
		}
		$data[$this->alias]['signer_signature'] = $signature['name'];
		$data[$this->alias]['signer_signed_date'] = $signature['date'];
		$data[$this->alias]['signed_date'] = date('Y-m-d H:i:s');
		
		return $this->save($data);
	}
	
	public function batchChangeStatus($ids = array(), $order = false, $data = array())
	{
		if(!$ids)
		{
			$this->modelError = __('No %s selected.', __('Media'));
			return false;
		}
		if(!$order)
		{
			$this->modelError = __('No %s selected.', __('Status'));
			return false;
		}
		
		// get the id of the order we want to move to.
		if(!$sanitize_status_id = $this->SanitizeStatus->idFromOrder($order))
		{
			$this->modelError = __('Unrecognized %s.', __('Status'));
			return false;
		}
		
		$saveData = array();
		$old_id_count = 0; 
		$new_id_count = 0;
		
		if($order == 1) // changing to 1
		{
			$old_id_count = count($ids);
			// filter for ones that are only a status 1
			$ids = $this->find('list', array(
				'recursive' => 0,
				'contain' => array('SanitizeStatus'),
				'fields' => array($this->alias.'.id', $this->alias.'.id'),
				'conditions' => array(
					$this->alias.'.id' => $ids,
					'SanitizeStatus.order' => 2,
				)
			));
			$new_id_count = count($ids);
			
			if(!$ids)
			{
				$this->modelError = __('None of the selected %s were eligable to have their %s changed. (2)', __('Media') , __('Status'));
				return false;
			}
			
			$saveData = array();
			foreach($ids as $id => $old_order)
			{
				$saveData[$id] = array_merge(array('id' => $id, 'sanitize_status_id' => $sanitize_status_id), $data);
			}
		}
		elseif($order == 2) 
		{
			$old_id_count = count($ids);
			// filter for ones that are only a status 1
			$ids = $this->find('list', array(
				'recursive' => 0,
				'contain' => array('SanitizeStatus'),
				'fields' => array($this->alias.'.id', $this->alias.'.id'),
				'conditions' => array(
					$this->alias.'.id' => $ids,
					'SanitizeStatus.order' => 1,
				)
			));
			$new_id_count = count($ids);
			
			if(!$ids)
			{
				$this->modelError = __('None of the selected %s were eligable to have their %s changed. (2)', __('Media') , __('Status'));
				return false;
			}
			
			$saveData = array();
			foreach($ids as $id => $old_order)
			{
				$saveData[$id] = array_merge(array('id' => $id, 'sanitize_status_id' => $sanitize_status_id), $data);
			}
		}
		elseif($order == 4) // changing to 4
		{
			$old_id_count = count($ids);
			// filter for ones that are only a status 1
			$ids = $this->find('list', array(
				'recursive' => 0,
				'contain' => array('SanitizeStatus'),
				'fields' => array($this->alias.'.id', $this->alias.'.id'),
				'conditions' => array(
					$this->alias.'.id' => $ids,
					'SanitizeStatus.order' => 3,
				)
			));
			$new_id_count = count($ids);
			
			if(!$ids)
			{
				$this->modelError = __('None of the selected %s were eligable to have their %s changed. (2)', __('Media') , __('Status'));
				return false;
			}
			
			$saveData = array();
			foreach($ids as $id => $old_order)
			{
				$saveData[$id] = array_merge(array('id' => $id, 'sanitize_status_id' => $sanitize_status_id), $data);
			}
		}
		
		if($saveData)
		{
			if(!$this->saveMany($saveData))
			{
				$this->modelError = __('Unable to save the changed %s of the selected %s' , __('Status'), __('Media'));
				return false;
			}
			return array($old_id_count, $new_id_count);
		}
		
		$this->modelError = __('None of the selected %s were eligable to have their %s changed. (0)', __('Media') , __('Status'));
		return false;
	}
	
	public function revertStatus($media_id = false, $new_status = 0)
	{
	}
	
	public function sendReviewerEmails()
	{
	// sends an email once/day at the end, letting all reviewers/managers know if/how much media is in an opened state
	}
	
	public function sendSignerEmails()
	{
	// sends an email once/day at the end, letting all signers/managers know if/how much media is in a closed state
	}
	
	public function sendTesterEmails()
	{
	// sends an email once/day at the end, letting all signers know if/how much media is in a closed state
	}
	
	public function sendEditEmail($id = false, $user_id = false)
	{
	 	if(!$id) return false;
	 	
		
		$this->recursive = 1;
		$media = $this->read(null, $id);
		
		$user = array();
		if($user_id)
		{
			$this->MediaAddedUser->recursive = -1;
			if($user = $this->MediaAddedUser->read(null, $user_id))
			{
				$user = $user['MediaAddedUser'];
			}
		}
		
		$emails = array();
		
		// Emails specific to this Media Entity
		if(isset($media['MediaAddedUser']['email']) and $media['MediaAddedUser']['email'])
		{
			$emails[$media['MediaAddedUser']['email']] = $media['MediaAddedUser']['email'];
		}
		if(isset($media['MediaModifiedUser']['email']) and $media['MediaModifiedUser']['email'])
		{
			$emails[$media['MediaModifiedUser']['email']] = $media['MediaModifiedUser']['email'];
		}
		if(isset($media['MediaReceivedUser']['email']) and $media['MediaReceivedUser']['email'])
		{
			$emails[$media['MediaReceivedUser']['email']] = $media['MediaReceivedUser']['email'];
		}
		if(isset($media['MediaClosedUser']['email']) and $media['MediaClosedUser']['email'])
		{
			$emails[$media['MediaClosedUser']['email']] = $media['MediaClosedUser']['email'];
		}
		if(isset($media['MediaOpenedUser']['email']) and $media['MediaOpenedUser']['email'])
		{
			$emails[$media['MediaOpenedUser']['email']] = $media['MediaOpenedUser']['email'];
		}
		
		// all Admin 
		$adminEmails = $this->MediaAddedUser->adminEmails();
		foreach($adminEmails as $adminEmail)
		{
			$emails[$adminEmail] = $adminEmail;
		}
	 	
	 	
	 	// rebuild it to use the EmailBehavior from the Utilities Plugin
	 	$this->Email_reset();
		
		// set the variables so we can use view templates
		$viewVars = array(
			'user' => $user,
			'media' => $media,
		);
		
		$this->Email_set('to', $emails);
		$this->Email_set('subject', __('Closed %s needs editing - ID: %s', __('Media'), $media['Media']['id']));
		$this->Email_set('viewVars', $viewVars);
		$this->Email_set('template', 'send_edit_email');
		
		return $this->Email_executeFull();
	}
	
	public function getPdfForm($id = false)
	{
		$this->modelError = false;
	 	
	 	if(!$id) 
	 	{
	 		$this->modelError = __('Unknown ID');
	 		return false;
	 	}
	 	
	 	
	 	$this->recursive = 0;
	 	$media = $this->read(null, $id);
	 	
	 	$serialnum = trim($media[$this->alias]['serial_number']);
	 	
	 	$removal_method = Configure::read('PDF.removal_method');
	 	$removal_method = str_replace('%NAME%', $media['MediaSanitizedUser']['name'], $removal_method);
	 	$removal_method = str_replace('%DATE%', $media[$this->alias]['sanitized_date'], $removal_method);
	 	$removal_method = str_replace('%ID%', $media[$this->alias]['id'], $removal_method);
	 	$removal_method = str_replace("\n", "\r", $removal_method);
	 	
	 	if(!$params = $this->PhpPdf_getPdfForm($id, $media, array(
	 		'pdf_template' => 'blank_data_removal_form.pdf',
	 		'pdf_filename' => 'data_removal_form-'. $id. ($serialnum?'-'. $serialnum:''). '.pdf',
	 		'field_map' => array(
	 			'NIH Decal' => 'Media.asset_tag',
	 			'Model' => 'Media.model',
	 			'Manufacturer' => 'Media.manufacturer',
	 			'Serial I' => 'Media.serial_number',
	 			'Date' => array('field' => 'Media.obtained', 'type' => 'date', 'format' => 'M j, Y'),
	 			'Status Code' => array('value' => 2),
	 			'IC' => array('value' => 'Example ORG'),
	 			'Method of Removal' => array('value' => $removal_method),
	 			'Printed or Typed Name of ISSO or Designee' => array('value' => Configure::read('PDF.ISSOfield')),
	 			'Phone' => array('value' => Configure::read('PDF.ISSOphone')),
	 			/*
	 				we aren't collecting this info 
	 			'Method of Removal' => '',
	 			'Status Code' => '', // will always be 2
	 			'Phone' => '',
	 			'Signature' => '', // this is the field that gets signed using the PIV Card
	 			*/
	 		),
	 	)))
	 	{
	 		if(!$this->modelError) $this->modelError = __('Unable to retrieve the Pdf Form.');
	 		return false;
	 	}
	 	return $params;
	}
	
	public function getMergedPdfForms($ids = array())
	{
		$ids = $this->find('list', array(
			'fields' => array($this->alias.'.id', $this->alias.'.form_filename'),
			'conditions' => array(
				$this->alias.'.id' => $ids,
				$this->alias.'.form_filename !=' => '',
			),
		));
		if(!$ids)
		{
			$this->modelError = __('None of the selected %s have Signed PDF Forms available to download.', __('Media'));
	 		return false;
		}
		if(!$params = $this->PhpPdf_mergePdfFiles($ids, 'data_removal_form-merged-'.date('Y-m-d-H-i') ))
		{
	 		if(!$this->modelError) $this->modelError = __('Unable to merge the Pdf Forms.');
	 		return false;
		}
	 	return $params;
	}
	
	public function checkPdfFormSerial($serial_num = false, $pdf_path = false)
	{
		if(!$serial_num)
			return false;
		if(!$pdf_path)
			return false;
		if(!is_readable($pdf_path))
			return false;
		
		$serial_num = trim($serial_num);
		
		$form_data = $this->PhpPdf_getFormData($pdf_path);
		
		if(!isset($form_data['short']['serial_i']))
		{
			$this->modelError = __('This PDF forms isn\'t the proper form. It doesn\'t have a serial number listed.');
			return false;
		}
		
		if($serial_num == $form_data['short']['serial_i'])
			return true;
		
		return false;
	}
	
	public function snapshotStats()
	{
		$entities = $this->Snapshot_dynamicEntities();
		return array();
	}
}