<?php
App::uses('AppController', 'Controller');

class MediaLogsController extends AppController 
{
	public function media_repurposed($media_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'MediaLog.media_id' => $media_id,
			'MediaLog.repurpose_status_id > ' => 0,
		);
		
//		$this->MediaLog->
		
		$this->MediaLog->recursive = 0;
		$this->paginate['order'] = array('MediaLog.log_created' => 'desc');
		$this->paginate['conditions'] = $this->MediaLog->conditions($conditions, $this->passedArgs); 
		$this->set('media_logs', $this->paginate());
	}
	
	public function admin_media($media_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'MediaLog.media_id' => $media_id,
		);
		
		$this->MediaLog->recursive = 0;
		$this->paginate['order'] = array('MediaLog.log_created' => 'desc');
		$this->paginate['conditions'] = $this->MediaLog->conditions($conditions, $this->passedArgs); 
		$this->set('media_logs', $this->paginate());
	}
	
	public function admin_view($id = null) 
	{
		$this->MediaLog->recursive = 1;
		if (!$media_log = $this->MediaLog->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Media Log')));
		}
		
		$this->MediaLog->Media->recursive = 1;
		if (!$media = $this->MediaLog->Media->read(null, $media_log['MediaLog']['media_id']))
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		$this->set('media_log', $media_log);
		$this->set('media', $media);
	}
}