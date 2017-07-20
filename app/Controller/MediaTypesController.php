<?php
App::uses('AppController', 'Controller');
/**
 * MediaTypes Controller
 *
 * @property MediaTypes $MediaType
 */
class MediaTypesController extends AppController 
{
	public function media($media_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays all Media Types
	 */
		$this->MediaType->Media->id = $media_id;
		if (!$this->MediaType->Media->exists())
		{
			throw new NotFoundException(__('Invalid Media'));
		}
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array('MediaMediaType.media_id' => $media_id);
		
		$this->MediaType->MediaMediaType->recursive = 0;
		$this->paginate['order'] = array('MediaType.name' => 'asc');
		$this->paginate['conditions'] = $this->MediaType->MediaMediaType->conditions($conditions, $this->passedArgs); 
		$this->set('media_media_types', $this->paginate());
	}

//
	public function manager_index() 
	{
	/**
	 * index method
	 *
	 * Displays all Media Types
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
		);
		
		$this->MediaType->recursive = -1;
		$this->paginate['order'] = array('MediaType.name' => 'asc');
		$this->paginate['conditions'] = $this->MediaType->conditions($conditions, $this->passedArgs); 
		$this->set('media_types', $this->paginate());
	}
	
	public function manager_add() 
	{
	/**
	 * add method
	 *
	 * @return void
	 */
		if ($this->request->is('post'))
		{
			$this->MediaType->create();
			
			if ($this->MediaType->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The Media Type has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Media Type could not be saved. Please, try again.'));
			}
		}
	}
	
	public function manager_edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->MediaType->id = $id;
		if (!$this->MediaType->exists()) 
		{
			throw new NotFoundException(__('Invalid Media Type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->MediaType->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Media Type has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Media Type could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->MediaType->read(null, $id);
		}
	}

//
	public function manager_delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
	 
		$this->MediaType->id = $id;
		if (!$this->MediaType->exists()) 
		{
			throw new NotFoundException(__('Invalid Media Type'));
		}

		if ($this->MediaType->delete()) 
		{
			$this->Session->setFlash(__('Media Type deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('Media Type was not deleted'));
		return $this->redirect($this->referer());
	}
}
