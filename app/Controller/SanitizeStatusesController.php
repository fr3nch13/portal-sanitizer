<?php
App::uses('AppController', 'Controller');
/**
 * SanitizeStatuses Controller
 *
 * @property SanitizeStatuses $SanitizeStatus
 */
class SanitizeStatusesController extends AppController 
{
	public function menu_main()
	{
		if ($this->request->is('requested')) 
		{
			$statuses = $this->SanitizeStatus->statuses();
			
			// format for the menu_items
			$items = array();
			$items[] = array(
				'title' => __('All'),
				'url' => array('controller' => 'media', 'action' => 'index', 'admin' => false, 'plugin' => false)
			);
			
			foreach($statuses as $order => $status)
			{	
				$items[] = array(
					'title' => $status,
					'url' => array('controller' => 'media', 'action' => 'index', $order, 'admin' => false, 'plugin' => false)
				);
			}
			return $items;
		}
	}

//
	public function manager_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->SanitizeStatus->recursive = -1;
		$this->paginate['order'] = array('SanitizeStatus.order' => 'asc');
		$this->paginate['conditions'] = $this->SanitizeStatus->conditions($conditions, $this->passedArgs); 
		$this->set('sanitize_statuses', $this->paginate());
	}
	
	public function manager_edit($id = null) 
	{
		$this->SanitizeStatus->id = $id;
		if (!$this->SanitizeStatus->exists()) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Sanitize')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->SanitizeStatus->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s %s has been saved', __('Sanitize'), __('Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s %s could not be saved. Please, try again.', __('Sanitize'), __('Status')));
			}
		}
		else
		{
			$this->request->data = $this->SanitizeStatus->read(null, $id);
		}
	}
	
	public function manager_sorted() 
	{
		if(!$this->request->is('ajax'))
		{
			throw new ForbiddenException(__('Invalid Request'));
		}
		// not an admin
		if(!in_array(AuthComponent::user('role'), array('admin')))
		{
			throw new ForbiddenException(__('Invalid Request'));
		}
		
		if ($this->request->is('post'))
		{
			if ($this->SanitizeStatus->changeOrders($this->request->data))
			{
				$results = array(
					'success' => true,
					'msg' => __('The %s %s orders have been saved', __('Sanitize'), __('Status')),
				);
			}
			else
			{
				$results = array(
					'success' => false,
					'msg' => __('The %s %s could not be saved. %s', __('Sanitize'), __('Status'), ($this->SanitizeStatus->modelError?'('.$this->SanitizeStatus->modelError.')':false) ),
				);
			}
			$this->set('results', $results);
			$this->set('_serialize', array('results'));
		}
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->SanitizeStatus->create();
			
			if ($this->SanitizeStatus->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s %s has been saved', __('Sanitize'), __('Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s %s could not be saved. Please, try again.', __('Sanitize'), __('Status')));
			}
		}
	}

	public function admin_toggle($field = null, $id = null)
	{
		if ($this->SanitizeStatus->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s %s has been saved', __('Sanitize'), __('Status')));
		}
		else
		{
			$this->Session->setFlash($this->SanitizeStatus->modelError);
		}
		
		return $this->redirect($this->referer());
	}
}
