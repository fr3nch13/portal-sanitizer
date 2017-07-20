<?php

class UpdateShell extends AppShell
{
	// the models to use
	public $uses = array('Media', 'MediaLog');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Update Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Update Shell runs all needed jobs to update production\'s database.'));
		
		$parser->addSubcommand('fix_media', array(
			'help' => __d('cake_console', 'Fixes issues with media records.'),
			'parser' => array(
				'arguments' => array(
//					'minutes' => array('help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'))
				)
			)
		));
		
		return $parser;
	}
	
	public function fix_media()
	{
		Configure::write('debug', 1);
		$_media = $this->Media->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'Media.released_user_id' => 0,
				'Media.sanitize_status_id' => 4,
			),
		));
		
		$saveMany = array();
		foreach($_media as $media)
		{
			$id = $media['Media']['id'];
			$saveMany[$id] = array(
				'id' => $id,
				'released_user_id' => $media['Media']['modified_user_id'],
				'released_date' => $media['Media']['modified'],
				'modified' =>  $media['Media']['modified'],
			);
		}
		
		$this->Media->logBypass = true;
		if($saveMany)
			$this->Media->saveMany($saveMany);
	}
}