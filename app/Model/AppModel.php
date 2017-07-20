<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

// load for all models
App::uses('AuthComponent', 'Controller/Component');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model 
{
	public $actsAs = array(
		'Containable', 
		'Utilities.Common', 
		'Utilities.Extractor', 
		'Utilities.Foapi', 
		'Utilities.Rules', 
		'Utilities.Shell', 
		'Search.Searchable', 
		'Ssdeep.Ssdeep', 
		'OAuthClient.OAuthClient' => array(
			'redirectUrl' => array('plugin' => false, 'controller' => 'users', 'action' => 'login', 'admin' => false)
		),
/*
		'Cacher.Cache' => array(
			'config' => 'database',
			'auto' => false,
		),
*/
		// used for avatar management
		'Upload.Upload' => array(
			'photo' => array(
				'deleteOnUpdate' => true,
				'thumbnailSizes' => array(
					'big' => '200x200',
					'medium' => '120x120',
					'thumb' => '80x80',
					'small' => '40x40',
					'tiny' => '16x16',
				),
			),
		),
    );
	
	public function stats()
	{
	/*
	 * Default placeholder if no stats function is available for a Model
	 */
		return array();
	}
}
