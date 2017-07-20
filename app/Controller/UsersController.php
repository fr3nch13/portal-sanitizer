<?php
// File: app/Controller/UsersController.php

class UsersController extends AppController
{
	public $allowAdminDelete = true;
	
	public function beforeFilter()
	{
		$this->Auth->allow(array(
			'login',
			'logout',
			'admin_login',
			'admin_logout',
		));
		
		return parent::beforeFilter();
	}

	public function login()
	{
		// have the OAuthClient component handle everything for this action
		return $this->OAuthClient->OAC_Login();
	}
	
	public function admin_login() 
	{
		return 	$this->login();
	}

	public function logout()
	{
		$this->Session->setFlash(__('You have successfully logged out.'));
		return $this->redirect($this->Auth->logout());
	}
	
	public function admin_logout() 
	{
		return 	$this->logout();
	}

	public function index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs); 
		$this->set('users', $this->paginate());
	}

	public function view($id = null)
	{
		if (!$user = $this->User->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		};
		
		$this->set('user', $user);
	}

	public function edit()
	{
		$this->User->id = AuthComponent::user('id');
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if(isset($this->request->query['flashmsg']))
		{
			$this->Session->setFlash($this->request->query['flashmsg']);
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				// update the Auth session data to reflect the changes
				if (isset($this->request->data['User']))
				{
					foreach($this->request->data['User'] as $k => $v)
					{
						if ($this->Session->check('Auth.User.'. $k))
						{
							$this->Session->write('Auth.User.'. $k, $v);
						}
					}
				}
				if (isset($this->request->data['UsersSetting']))
				{
					foreach($this->request->data['UsersSetting'] as $k => $v)
					{
						$this->Session->write('Auth.User.UsersSetting.'. $k, $v);
					}
				}
				
				$this->Session->setFlash(__('Your settings have been updated.'));
				// go back to this form 
				return $this->redirect(array('action' => 'edit'));
			}
			else
			{
				$this->Session->setFlash(__('We could not update your settings. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $user;
		}
		
		$this->set('sanitize_statuses', $this->User->MediaAddedUser->SanitizeStatus->statuses(true, 2) );
	}

	public function avatar()
	{
		$this->User->id = AuthComponent::user('id');
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				// update the Auth session data to reflect the changes
				if (isset($this->User->afterdata['User']))
				{
					foreach($this->User->afterdata['User'] as $k => $v)
					{
						if (SessionComponent::check('Auth.User.'. $k))
						{
							SessionComponent::write('Auth.User.'. $k, $v);
						}
					}
					if(isset($this->User->afterdata['User']['photo']))
					{
						SessionComponent::write('Auth.User.photo', $this->User->afterdata['User']['photo']);
					}
				}
				
				$this->Session->setFlash(__('Your avatar has been updated.'));
				// go back to this form 
				return $this->redirect(array('action' => 'edit'));
			}
			else
			{
				$this->Session->setFlash(__('We could not update your avatar. Please, try again.'));
			}
		}
		else
		{
			$this->User->recursive = 0;
			$this->request->data = $this->User->read(null, $this->User->id);
			unset($this->request->data['User']['password']);
		}
	}

	public function manager_index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs); 
		$this->set('users', $this->paginate());
	}

	public function manager_view($id = null)
	{
		if (!$user = $this->User->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		};
		
		$this->set('user', $user);
	}

	public function manager_add()
	{
		if ($this->request->is('post'))
		{
			$this->User->create();
			if ($this->User->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
	}

	public function manager_edit($id = null)
	{
		$this->User->id = $id;
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				if($this->User->id == AuthComponent::user('id'))
				{
					// update the Auth session data to reflect the changes
					if (isset($this->request->data['User']))
					{
						foreach($this->request->data['User'] as $k => $v)
						{
							if ($this->Session->check('Auth.User.'. $k))
							{
								$this->Session->write('Auth.User.'. $k, $v);
							}
						}
					}
				}
				
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		else
		{
			$this->request->data = $user;
		}
	}

	public function manager_password($id = null)
	{
		$this->User->id = $id;
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->save($this->request->data))
			{
				$this->Session->setFlash(__('The password for the %s has been saved.', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The password validation for the %s did not match. Please, try again.', __('User')));
				// go back to the settings page 
				return $this->redirect(array('action' => 'edit'));
			}
		}
		else
		{
			// if they're trying to view this page, send them to the edit form
			return $this->redirect(array('action' => 'edit', $id));
		}
	}

	public function manager_toggle($field = null, $id = null)
	{
		if ($this->User->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s has been updated.', __('User')));
		}
		else
		{
			$this->Session->setFlash($this->User->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_admin()
	{
	}

	public function admin_index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs); 
		$this->set('users', $this->paginate());
	}

	public function admin_view($id = null)
	{
		if (!$user = $this->User->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		};
		
		$this->set('user', $user);
	}

	public function admin_add()
	{
		if ($this->request->is('post'))
		{
			$this->User->create();
			if ($this->User->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
	}

	public function admin_edit($id = null)
	{
		$this->User->id = $id;
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				if($this->User->id == AuthComponent::user('id'))
				{
					// update the Auth session data to reflect the changes
					if (isset($this->request->data['User']))
					{
						foreach($this->request->data['User'] as $k => $v)
						{
							if ($this->Session->check('Auth.User.'. $k))
							{
								$this->Session->write('Auth.User.'. $k, $v);
							}
						}
					}
				}
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		else
		{
			$this->request->data = $user;
		}
	}

	public function admin_password($id = null)
	{
		$this->User->id = $id;
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->save($this->request->data))
			{
				$this->Session->setFlash(__('The password for the %s has been saved.', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The password validation for the %s did not match. Please, try again.', __('User')));
				// go back to the settings page 
				return $this->redirect(array('action' => 'edit'));
			}
		}
		else
		{
			// if they're trying to view this page, send them to the edit form
			return $this->redirect(array('action' => 'edit', $id));
		}
	}

	public function admin_toggle($field = null, $id = null)
	{
		if ($this->User->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s has been updated.', __('User')));
		}
		else
		{
			$this->Session->setFlash($this->User->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	/// Config for the app
	public function admin_config()
	{
		// check that we can read/write to the config
		if(!$this->User->configCheck())
		{
			throw new InternalErrorException(__('Error with the config file: "%s". Error: %s. Please check the permissions for writing to this file.', $this->User->configPath, $this->User->configError));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			// check that we can read/write to the config
			if(!$this->User->configCheck(true))
			{
				throw new InternalErrorException(__('Error with the config file: "%s". Error: %s. Please check the permissions for writing to this file.', $this->User->configPath, $this->User->configError));
			}
			if ($this->User->configSave($this->request->data))
			{
				$this->Session->setFlash(__('The config has been saved'));
				return $this->redirect(array('action' => 'config'));
			}
			else
			{
				$this->Session->setFlash(__('The config could not be saved. Please, try again.'));
				return $this->redirect(array('action' => 'config'));
			}
		}
		
		$this->set('fields', $this->User->configKeys());
		
		$this->request->data = $this->User->configRead();
	}
}