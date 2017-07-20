<?php
App::uses('AppController', 'Controller');
/**
 * RepurposeStatuses Controller
 *
 * @property RepurposeStatuses $RepurposeStatus
 */
class RepurposeStatusesController extends AppController 
{
	public function menu_main()
	{
		if ($this->request->is('requested')) 
		{
			$statuses = $this->RepurposeStatus->statuses();
			
			// format for the menu_items
			$items = array();
			$items[] = array(
				'title' => __('All'),
				'url' => array('controller' => 'media', 'action' => 'repurposed', 'admin' => false, 'plugin' => false)
			);
			
			foreach($statuses as $order => $status)
			{	
				$items[] = array(
					'title' => $status,
					'url' => array('controller' => 'media', 'action' => 'repurposed', $order, 'admin' => false, 'plugin' => false)
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
		
		$this->RepurposeStatus->recursive = -1;
		$this->paginate['order'] = array('RepurposeStatus.order' => 'asc');
		$this->paginate['conditions'] = $this->RepurposeStatus->conditions($conditions, $this->passedArgs); 
		$this->set('repurpose_statuses', $this->paginate());
	}
	
	public function manager_edit($id = null) 
	{
		$this->RepurposeStatus->id = $id;
		if (!$this->RepurposeStatus->exists()) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Repurpose')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->RepurposeStatus->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s %s has been saved', __('Repurpose'), __('Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s %s could not be saved. Please, try again.', __('Repurpose'), __('Status')));
			}
		}
		else
		{
			$this->request->data = $this->RepurposeStatus->read(null, $id);
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
			if ($this->RepurposeStatus->changeOrders($this->request->data))
			{
				$results = array(
					'success' => true,
					'msg' => __('The %s %s orders have been saved', __('Repurpose'), __('Status')),
				);
			}
			else
			{
				$results = array(
					'success' => false,
					'msg' => __('The %s %s could not be saved. %s', __('Repurpose'), __('Status'), ($this->RepurposeStatus->modelError?'('.$this->RepurposeStatus->modelError.')':false) ),
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
			$this->RepurposeStatus->create();
			
			if ($this->RepurposeStatus->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s %s has been saved', __('Repurpose'), __('Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s %s could not be saved. Please, try again.', __('Repurpose'), __('Status')));
			}
		}
	}

	public function admin_toggle($field = null, $id = null)
	{
		if ($this->RepurposeStatus->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s %s has been saved', __('Repurpose'), __('Status')));
		}
		else
		{
			$this->Session->setFlash($this->RepurposeStatus->modelError);
		}
		
		return $this->redirect($this->referer());
	}
}
