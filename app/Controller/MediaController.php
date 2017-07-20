<?php
App::uses('AppController', 'Controller');

class MediaController extends AppController 
{

	public function isAuthorized($user = array())
	{
		// All registered users can add and view media
		if (in_array($this->action, array('index', 'add', 'view', 'edit', 'close'))) 
		{
			if(in_array(AuthComponent::user('role'), array('basic')))
			{
				if($this->action == 'index')
				{
					return $this->redirect(array('action' => 'index', 'basic' => true));
				}
				return false;
			}
			return true;
		}
		
		// only the reviewer can change the state and delete
		if (in_array($this->action, array('toggle', 'delete'))) 
		{
			if(in_array(AuthComponent::user('role'), array('admin', 'reviewer')))
			{
				return true;
			}
		}

		return parent::isAuthorized($user);
	}
	
	public function beforeFilter()
	{
		$this->set('mediaStatuses', $this->Media->SanitizeStatus->statuses());
		$this->set('repurposeStatuses', $this->Media->RepurposeStatus->statuses());
		return parent::beforeFilter();
	}

	public function index($status = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Media.repurpose_status_id' => 0,
		);
		
		if($status !== false)
		{
			if(stripos($status, ',') !== false)
			{
				$status = explode(',', $status);
			}
			$conditions['SanitizeStatus.order'] = $status;
		}
		
		if ($this->request->is('requested')) 
		{
			$_media = $this->Media->find('all', array(
				'recursive' => 0,
				'conditions' => $conditions,
			));
			
			// format for the menu_items
			$items = array();
			
			foreach($_media as $media)
			{
				$title = $media['Media']['id']. '-';
				
				$items[] = array(
					'title' => $media['Media']['id']. ' : '. (trim($media['Media']['serial_number'])?$media['Media']['serial_number']:__('(Empty)')). ' : '. (trim($media['Media']['asset_tag'])?$media['Media']['asset_tag']:__('(Empty)')),
					'url' => array('controller' => 'media', 'action' => 'view', $media['Media']['id'], 'admin' => false, 'plugin' => false)
				);
			}
			return $items;
		}
		else
		{
			$this->Media->recursive = 0;
			$this->paginate['order'] = array('Media.created' => 'desc');
			$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
			$this->set('_media', $this->paginate());
		}
	}

	public function repurposed($status = false) 
	{
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
			'Media.repurpose_status_id >' => 0,
		);
		
		if($status !== false)
		{
			if(stripos($status, ',') !== false)
			{
				$status = explode(',', $status);
			}
			$conditions['RepurposeStatus.order'] = $status;
		}
		
		$this->Media->recursive = 0;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}

	public function user($user_id = false) 
	{
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
			'Media.repurpose_status_id' => 0,
			'OR' => array(
				'Media.added_user_id' => $user_id,
				'Media.modified_user_id' => $user_id,
				'Media.opened_user_id' => $user_id,
				'Media.sanitized_user_id' => $user_id,
				'Media.signed_user_id' => $user_id,
				'Media.released_user_id' => $user_id,
				'Media.repurposed_user_id' => $user_id,
				'Media.tested_user_id' => $user_id,
			),
		);
		
		$this->Media->recursive = 0;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Media->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		$conditions[] = $this->Media->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Media');
		
		$this->Media->recursive = 0;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		// reorganize the sanitize statuses if this media is repurposed
		if(isset($media['RepurposeStatus']) and $media['RepurposeStatus']['id'])
		{
			$mediaStatuses = $this->Media->SanitizeStatus->statuses(false, 2);
			$mediaStatuses[] = $media['RepurposeStatus']['name'];
			$this->set('mediaStatuses', $mediaStatuses);
		}
		
		$this->set('media', $media);
	}
	
	public function mini_view($id = null) 
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		$this->set('media', $media);
		$this->layout = 'ajax_nodebug';
	}
	
	public function get_form($id = null)
	{
		$this->Media->recursive = 0;
		if (!$params = $this->Media->getPdfForm($id))
		{
			throw new NotFoundException(__('Invalid %s (%s)', __('PDF Form'), $this->Media->modelError));
		}
		
		$this->viewClass = 'Media';
		$this->set($params);
	}

	public function add($toedit = false) 
	{
		if ($this->request->is('post'))
		{
			$this->Media->create();
			$this->request->data['Media']['added_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['added_date'] = date('Y-m-d H:i:s');
			$this->request->data['Media']['created'] = date('Y-m-d H:i:s');
			$this->request->data['Media']['modified'] = false;
			
			if ($this->Media->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Media')));
				$redirect = array('action' => 'view', $this->Media->id);
				if($toedit) $redirect = array('action' => 'edit', $this->Media->id);
				$this->bypassReferer = true;
				return $this->redirect($redirect);
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			$this->request->data['Media']['sanitize_status_id'] = AuthComponent::user('UsersSetting.default_sanitize_status_id');
		}
		
		$this->set('media_types', $this->Media->MediaType->find('list', array('order' => 'MediaType.name')));
		$this->set('sanitize_statuses', $this->Media->SanitizeStatus->statuses(true, 2) );
	}

	public function edit($id = null) 
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			
			if ($this->Media->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated', __('Media')));
				return $this->redirect(array('action' => 'view', $this->Media->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			if($media['SanitizeStatus']['order'] > 2)
			{
				$this->Session->setFlash(__('The %s can\'t be edited when in a closed state. Have an Admin, Manager, or Reviewer open it.', __('Media')));
				// send an email to the users involved, and admins
//// commented out until we need it, this should go to managers, and reviewers
//				$this->Media->sendEditEmail($this->Media->id, AuthComponent::user('id'));
				return $this->redirect(array('action' => 'view', $this->Media->id));
			}
			
			$this->Media->recursive = 1;
			$this->request->data = $this->Media->read(null, $this->Media->id);
		}
		
		$this->set('media_types', $this->Media->MediaType->find('list', array('order' => 'MediaType.name')));
		$this->set('sanitize_statuses', $this->Media->SanitizeStatus->statuses(true, 2) );
	}
	
	public function mark_repurposed($id = null)
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		$this->set('media', $media);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['repurposed_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['repurposed_date'] = date('Y-m-d H:i:s');
			if ($this->Media->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated', __('Media')));
				return $this->redirect(array('action' => 'view', $this->Media->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			$this->Media->recursive = 0;
			$this->request->data = $media;
		}
		$this->set('repurpose_statuses', $this->Media->RepurposeStatus->find('list'));
	}
	
	public function status_opened($id = null)
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		$this->set('media', $media);
		
		if(!in_array($media['SanitizeStatus']['order'], array(2))) // it's not in the first step
		{
			$this->Session->setFlash(__('Unable to mark %s this as %s , its current %s won\'t allow it.', 
				__('Media'), 
				__('Opened'), 
				__('Status')
			));
			return $this->redirect($referer);
		}
		
		// mark as released
		$this->Media->id = $id;
		$this->Media->data = array(
			'modified_user_id' => AuthComponent::user('id'),
			'opened_user_id' => AuthComponent::user('id'),
			'opened_date' => date('Y-m-d H:i:s'),
			'sanitize_status_id' => $this->Media->SanitizeStatus->idFromOrder(1),
		);
			
		if ($this->Media->save($this->Media->data))
		{
			$this->Session->setFlash(__('The %s has been updated', __('Media')));
		}
		else
		{
			$this->Session->setFlash(__('The %s could not be saved. Please, try again. (%s)', __('Media'), $this->Media->modelError));
		}
		
		return $this->redirect($this->referer());
	}
	
	public function status_sanitized($id = null)
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		$this->set('media', $media);
		
		if(!in_array($media['SanitizeStatus']['order'], array(1))) // it's not in the first step
		{
			$this->Session->setFlash(__('Unable to mark %s this as %s , its current %s won\'t allow it.', 
				__('Media'), 
				__('Sanitized'), 
				__('Status')
			));
			return $this->redirect(array('action' => 'index'));
		}
		
		// mark as released
		$this->Media->id = $id;
		$this->Media->data = array(
			'modified_user_id' => AuthComponent::user('id'),
			'sanitized_user_id' => AuthComponent::user('id'),
			'sanitized_date' => date('Y-m-d H:i:s'),
			'sanitize_status_id' => $this->Media->SanitizeStatus->idFromOrder(2),
		);
			
		if ($this->Media->save($this->Media->data))
		{
			$this->Session->setFlash(__('The %s has been updated', __('Media')));
		}
		else
		{
			$this->Session->setFlash(__('The %s could not be saved. Please, try again. (%s)', __('Media'), $this->Media->modelError));
		}
		
		return $this->redirect($this->referer());
	}
	
	public function status_sign($id = null)
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		$this->set('media', $media);
		
		$referer = $this->referer();
		
		if(!in_array($media['SanitizeStatus']['order'], array(2, 3))) // it's not closed
		{
			$this->Session->setFlash(__('Unable to %s this %s, its current %s won\'t allow it.', 
				($media['SanitizeStatus']['order']==2?__('Sign'):__('Re-Sign')), 
				__('Media'), 
				__('Status')
			));
			return $this->redirect($referer);
		}
		
		
		$this->Media->validator()->add('file', 'required', array(
    			'rule' => 'uploadError',
    			'message' => __('Please upload the Signed PDF Form.'),
		));
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['signed_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['signed_date'] = date('Y-m-d H:i:s');
			$this->request->data['Media']['sanitize_status_id'] = $this->Media->SanitizeStatus->idFromOrder(3);
			
			$this->Media->manageUploads = array(
				'db_field' => 'form_filename'
			);
			
			if ($this->Media->sign($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated', __('Media')));
				return $this->redirect(array('action' => 'view', $this->Media->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again. Error: (%s)', __('Media'), $this->Media->modelError));
			}
		}
		
		$this->Media->recursive = 0;
		$this->request->data = $media;
	}
	
	public function status_released($id = null)
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		if(!in_array($media['SanitizeStatus']['order'], array(3))) // it's not signed
		{
			$this->Session->setFlash(__('Unable to %s this %s, its current %s won\'t allow it.', 
				__('Release'), 
				__('Media'), 
				__('Status')
			));
			return $this->redirect($referer);
		}
		
		// mark as released
		$this->Media->id = $id;
		$this->Media->data = array(
			'modified_user_id' => AuthComponent::user('id'),
			'released_user_id' => AuthComponent::user('id'),
			'released_date' => date('Y-m-d H:i:s'),
			'sanitize_status_id' => $this->Media->SanitizeStatus->idFromOrder(4),
		);
			
		if ($this->Media->save($this->Media->data))
		{
			$this->Session->setFlash(__('The %s has been updated', __('Media')));
		}
		else
		{
			$this->Session->setFlash(__('The %s could not be saved. Please, try again. (%s)', __('Media'), $this->Media->modelError));
		}
		
		return $this->redirect($this->referer());
	}
	
	public function multiselect() 
	{
		if(!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		$redirect = $this->referer();
		
		// get the ids
		$ids = array();
		if(isset($this->request->data['multiple']))
		{
			foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[$id] = $id; }
			$this->request->data['multiple'] = $ids;
		}
		
		$flashMsg = __('No recognized option was selected.');
		
		
		if($this->request->data['Media']['multiselect_option'] == 'mark_sanitized')
		{
			$data = array(
				'modified_user_id' => AuthComponent::user('id'),
				'sanitized_user_id' => AuthComponent::user('id'),
				'sanitized_date' => date('Y-m-d H:i:s'),
			);
			if($results = $this->Media->batchChangeStatus($ids, 2, $data))
				$flashMsg = __('The %s of the %s has been changed. (%s of %s)', __('Status'), __('Media'), $results[1], $results[0]);
			else
				$flashMsg = __('Unable to change the %s of the %s, reason: %s', __('Status'), __('Media'), $this->Media->modelError);
		}
		elseif($this->request->data['Media']['multiselect_option'] == 'mark_opened')
		{
			$data = array(
				'modified_user_id' => AuthComponent::user('id'),
				'opened_user_id' => AuthComponent::user('id'),
				'opened_date' => date('Y-m-d H:i:s'),
			);
			if($results = $this->Media->batchChangeStatus($ids, 1, $data))
				$flashMsg = __('The %s of the %s has been changed. (%s of %s)', __('Status'), __('Media'), $results[1], $results[0]);
			else
				$flashMsg = __('Unable to change the %s of the %s, reason: %s', __('Status'), __('Media'), $this->Media->modelError);
		}
		elseif($this->request->data['Media']['multiselect_option'] == 'multisign')
		{
			// make sure this user can do this
			if(!AuthComponent::user('signer'))
			{
				$flashMsg = __('You don\'t have permission to %s the %s.', __('Sign'), __('Media'));
				$this->Session->setFlash($flashMsg);
				return $this->redirect($redirect);
			}
			Cache::write('Multiselect_Signing_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$flashMsg = __('Please upload the %s PDF Forms for the listed %s.', __('Signed'), __('Media'));
			$redirect = array('action' => 'multisign');
		}
		elseif($this->request->data['Media']['multiselect_option'] == 'mark_released')
		{
			$data = array(
				'modified_user_id' => AuthComponent::user('id'),
				'released_user_id' => AuthComponent::user('id'),
				'released_date' => date('Y-m-d H:i:s'),
			);
			$flashMsg = __('You don\'t have permission to change the selected %s to this status', __('Media'));
			if(in_array(AuthComponent::user('role'), array('admin', 'reviewer', 'manager')))
			{
				if($results = $this->Media->batchChangeStatus($ids, 4, $data))
					$flashMsg = __('The %s of the %s has been changed. (%s of %s)', __('Status'), __('Media'), $results[1], $results[0]);
				else
					$flashMsg = __('Unable to change the %s of the %s, reason: %s', __('Status'), __('Media'), $this->Media->modelError);
			}
			
		}
		elseif($this->request->data['Media']['multiselect_option'] == 'batchdownload')
		{
			if($params = $this->Media->getMergedPdfForms($ids, 4))
			{
		
				$this->viewClass = 'Media';
				$this->set($params);
				return $this->render();
			}
			else
				$flashMsg = __('Unable to merge all of the PDF Forms, reason: %s', $this->Media->modelError);
			
		}
		
		$this->Session->setFlash($flashMsg);
		$this->bypassReferer = true;
		$this->redirect($redirect);
	}
	
	public function multisign()
	{
		$sessionData = Cache::read('Multiselect_Signing_'. AuthComponent::user('id'), 'sessions');
		$this->Media->validator()->add('file', 'required', array(
    			'rule' => 'uploadError',
    			'message' => __('Please upload the Signed PDF Form.'),
		));
			
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$sanitize_status_id = $this->Media->SanitizeStatus->idFromOrder(3);
			foreach($this->request->data as $i => $data)
			{
				$this->request->data[$i]['Media']['modified_user_id'] = AuthComponent::user('id');
				$this->request->data[$i]['Media']['signed_user_id'] = AuthComponent::user('id');
				$this->request->data[$i]['Media']['signed_date'] = date('Y-m-d H:i:s');
				$this->request->data[$i]['Media']['sanitize_status_id'] = $sanitize_status_id;
			}
			
			$this->Media->manageUploads = array(
				'db_field' => 'form_filename'
			);
			
			if ($this->Media->sign($this->request->data))
			{
				Cache::delete('Multiselect_Signing_'. AuthComponent::user('id'), 'sessions');
				$this->Session->setFlash(__('The %s has been updated', __('Media')));
				return $this->redirect(array('action' => 'index', 3));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again. (%s)', __('Media'), $this->Media->modelError));
				$this->request->data['Media'] = array();
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			foreach($sessionData['multiple'] as $id => $selected)
			{
				if($selected) $ids[] = $id;
			}
		}
		
		$conditions = array('Media.id' => $ids);
		$this->Media->recursive = 0;
		$this->paginate['contain'] = array('SanitizeStatus');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs);
		$this->set('_media', $this->paginate());
	}

	public function basic_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Media.repurpose_status_id' => 0,
			'SanitizeStatus.order <' => 3,
		);
		
		$this->Media->searchFields = array(
			'Media.serial_number',
			'SanitizeStatus.name',
			'MediaType.name',
			'MediaAddedUser.name',
			'MediaAddedUser.email',
			'MediaModifiedUser.name',
			'MediaModifiedUser.email',
		);
		
		$this->Media->recursive = 0;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}
	
	public function basic_add($last_sanitize_status_id = false, $last_media_type_id = false) 
	{
		if ($this->request->is('post'))
		{
			$this->Media->create();
			$this->request->data['Media']['added_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['added_date'] = date('Y-m-d H:i:s');
			$this->request->data['Media']['created'] = date('Y-m-d H:i:s');
			$this->request->data['Media']['modified'] = false;
			
			$this->request->data['Media']['tags'] = 'Basic Added';
			
			$last_sanitize_status_id = $this->request->data['Media']['sanitize_status_id'];
			$last_media_type_id = $this->request->data['Media']['media_type_id'];
			
			if ($this->Media->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Media')));
				return $this->redirect(array('action' => 'add', $last_sanitize_status_id, $last_media_type_id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			$this->request->data['Media']['sanitize_status_id'] = AuthComponent::user('UsersSetting.default_sanitize_status_id');
			if($last_sanitize_status_id)
				$this->request->data['Media']['sanitize_status_id'] = $last_sanitize_status_id;
			if($last_media_type_id)
				$this->request->data['Media']['media_type_id'] = $last_media_type_id;
		}
		
		$this->set('media_types', $this->Media->MediaType->find('list', array('order' => 'MediaType.name')));
		$this->set('sanitize_statuses', $this->Media->SanitizeStatus->statuses(true, 2) );
	}
	
	public function basic_edit($id = null) 
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			
			if ($this->Media->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated', __('Media')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			if($media['SanitizeStatus']['order'] > 2)
			{
				$this->Session->setFlash(__('The %s can\'t be edited when in a closed state. Have an Admin, Manager, or Reviewer open it.', __('Media')));
				// send an email to the users involved, and admins
				$this->Media->sendEditEmail($this->Media->id, AuthComponent::user('id'));
				return $this->redirect(array('action' => 'index'));
			}
			
			$this->Media->recursive = 1;
			$this->request->data = $this->Media->read(null, $this->Media->id);
		}
		
		$this->set('media_types', $this->Media->MediaType->find('list', array('order' => 'MediaType.name')));
		$this->set('sanitize_statuses', $this->Media->SanitizeStatus->statuses(true, 2) );
	}

	public function manager_user($user_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				'Media.added_user_id' => $user_id,
				'Media.modified_user_id' => $user_id,
				'Media.opened_user_id' => $user_id,
				'Media.sanitized_user_id' => $user_id,
				'Media.signed_user_id' => $user_id,
				'Media.released_user_id' => $user_id,
				'Media.repurposed_user_id' => $user_id,
				'Media.tested_user_id' => $user_id,
			),
		);
		
		$this->Media->recursive = 0;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}

	public function admin_user($user_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				'Media.added_user_id' => $user_id,
				'Media.modified_user_id' => $user_id,
				'Media.opened_user_id' => $user_id,
				'Media.sanitized_user_id' => $user_id,
				'Media.signed_user_id' => $user_id,
				'Media.released_user_id' => $user_id,
				'Media.repurposed_user_id' => $user_id,
				'Media.tested_user_id' => $user_id,
			),
		);
		
		$this->Media->recursive = 0;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}
	
	public function admin_change_status($media_id = false)
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $media_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['modified'] = date('Y-m-d H:i:s');
			
			if ($this->Media->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated', __('Media')));
				$this->bypassReferer = true;
				return $this->redirect(array('action' => 'view', $this->Media->id, 'admin' => false));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			$this->request->data = $media;
		}
		$this->set('sanitize_statuses', $this->Media->SanitizeStatus->statuses(true, $this->request->data['SanitizeStatus']['order']) );
	}
	
	public function admin_mark_tested($id = null)
	{
		$this->Media->recursive = 0;
		if (!$media = $this->Media->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['tested_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['tested_date'] = date('Y-m-d H:i:s');
			if ($this->Media->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated', __('Media')));
				return $this->redirect(array('action' => 'view', $this->Media->id, 'admin' => false));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			$this->Media->recursive = 0;
			$this->request->data = $media;
		}
		$this->set('test_statuses', $this->Media->TestStatus->find('list'));
	}
	
	public function admin_unrepurpose($id = null)
	{
		$this->Media->id = $id;
		if (!$this->Media->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		$this->Media->data = array(
			'repurpose_status_id' => 0,
			'repurposed_user_id' => AuthComponent::user('id'),
			'repurposed_date' => date('Y-m-d H:i:s'),
		);
		
		if($this->Media->save($this->Media->data))
		{
			$this->Session->setFlash(__('Removed repurposed status from %s.', __('Media')));
		}
		else
		{
			$this->Session->setFlash(__('Unable to remove repurposed status from %s.', __('Media')));
		}
		return $this->redirect($this->referer());
	}

	public function admin_delete($id = null) 
	{
		$this->Media->id = $id;
		if (!$this->Media->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}

		if ($this->Media->delete()) 
		{
			$this->Session->setFlash(__('%s Deleted', __('Media')));
			return $this->redirect(array('controller' => 'media', 'action' => 'index', 'admin' => false));
		}
		
		$this->Session->setFlash(__('%s was not deleted', __('Media')));
		return $this->redirect($this->referer());
	}
}