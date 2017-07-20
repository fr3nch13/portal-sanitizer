<?php

class CronShell extends AppShell
{
	// the models to use
	public $uses = array('User', 'LoginHistory', 'SanitizeStatus', 'RepurposeStatus', 'Media');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Cron Shell');
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
		
		$parser->description(__d('cake_console', __('The Cron Shell runs all needed cron jobs') ));
		
		$parser->addSubcommand('failed_logins', array(
			'help' => __d('cake_console', 'Emails a list of failed logins to the admins and users every 10 minutes'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 10 minutes ago.'),
						'short' => 'm',
						'default' => 10,
					),
				),
			),
		));
		
		$parser->addSubcommand('sanitize_status_reminders', array(
			'help' => __d('cake_console', __('Sends a reminder email when there are %s assigned to a %s', __('Media'), __('Sanitize Status')) ),
		));
		
		$parser->addSubcommand('repurpose_status_reminders', array(
			'help' => __d('cake_console', __('Sends a reminder email when there are %s assigned to a %s', __('Media'), __('Repurpose Status')) ),
		));
		
		return $parser;
	}
	
	public function failed_logins()
	{
	/*
	 * Emails a list of failed logins to the admins every 5 minutes
	 * Only sends an email if there was a failed login
	 * Everything is taken care of in the Task
	 */
		$FailedLogins = $this->Tasks->load('Utilities.FailedLogins')->execute($this);
	}
	
	public function sanitize_status_reminders()
	{
		$this->SanitizeStatus->cron_sanitize_status_reminders();
	}
	
	public function repurpose_status_reminders()
	{
		$this->RepurposeStatus->cron_repurpose_status_reminders();
	}
}