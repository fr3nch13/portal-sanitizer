<?php
App::uses('AppController', 'Controller');

class TestStatusesController extends AppController 
{

	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->TestStatus->recursive = -1;
		$this->paginate['order'] = array('TestStatus.name' => 'asc');
		$this->paginate['conditions'] = $this->TestStatus->conditions($conditions, $this->passedArgs); 
		$this->set('test_statuses', $this->paginate());
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->TestStatus->create();
			
			if ($this->TestStatus->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Test Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Test Status')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		$this->TestStatus->id = $id;
		if (!$this->TestStatus->exists()) 
		{
			throw new NotFoundException(__('Invalid Media Type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->TestStatus->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Test Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Test Status')));
			}
		}
		else
		{
			$this->request->data = $this->TestStatus->read(null, $id);
		}
	}

	public function admin_delete($id = null) 
	{
		$this->TestStatus->id = $id;
		if (!$this->TestStatus->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Test Status')));
		}

		if ($this->TestStatus->delete()) 
		{
			$this->Session->setFlash(__('%s deleted', __('Test Status')));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('%s was not deleted', __('Test Status')));
		return $this->redirect($this->referer());
	}
}
