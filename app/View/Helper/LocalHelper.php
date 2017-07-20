<?php

// app/View/Helper/WrapHelper.php
App::uses('AppHelper', 'View/Helper');

/*
 * A helper used specifically for this app
 */
class LocalHelper extends AppHelper 
{
	
	public $statuses = array(
		0 => 'Opened',
		1 => 'Closed',
		2 => 'Signed',
		3 => 'Tested',
	);
	
	public $repurposeStatuses = array();
	
	public function userRole($role = false)
	{
		if($role == 'reviewer')
			$role = __('CSG Reviewer');
		return $role;
	}
	
	public function repurposeStatus($status = false)
	{
		$statuses = $this->repurposeStatuses;
		if(isset($this->_View->viewVars['repurposeStatuses']))
			$statuses = $this->_View->viewVars['repurposeStatuses'];
		
		if($status !== false)
		{
			return (isset($statuses[$status])?$statuses[$status]:false);
		}
		
		return $statuses;
	}
	
	public function status($status = false)
	{
		$statuses = $this->statuses;
		if(isset($this->_View->viewVars['mediaStatuses']))
			$statuses = $this->_View->viewVars['mediaStatuses'];
		
		if($status !== false)
		{
			return (isset($statuses[$status])?$statuses[$status]:false);
		}
		
		return $statuses;
	}
	
	public function availableStatuses($status = false)
	{
	// used with Media::admin_change_status();
		$out = array();
		if($status) // there aren't any available ones before Open/0
		{
			$statuses = $this->status();
			foreach($statuses as $status_id => $status_name)
			{
				if($status_id < $status)
					$out[$status_id] = $status_name;
			}
		}
		return $out;
	}
	
	public function statusBarSteps($status = false, $repurposed = false)
	{
		if($status === false)
			return false;
		
		if($repurposed)
			$status = 3;
		
		$statuses = $this->status();
		
		$steps = array();
		foreach($statuses as $status_id => $status_name)
		{
			$steps[$status_id] = array(
				'title' => $status_name,
				'completed' => (($status_id <= $status)?true:false),
			);
		}
		return $steps;
	}
	
	public function tested($tested = 0, $list = false)
	{
		$tested_options = array(
			0 => __('Not Tested'),
			1 => __('Test Failed'),
			2 => __('Test Succeeded'),
		);
		if($list) return $tested_options;
		
		if(isset($tested_options[$tested])) return $tested_options[$tested];
		
		return __('Test Unknown'); // should never return this
	}
	
	public function emailOptions2($option = false)
	{
		$options = Configure::read('Options.review_state_email');
		if($option === false)
		{
			return $options;
		}
		if(!isset($options[$option]))
		{
			return false;
		}
		return $options[$option];
	}
	
	public function emailTimes($selected = false)
	{
		if($selected === null) return ' '; // not even midnight is selected
		if($selected !== false)
		{
			$selected = (int)$selected;
		}
		$review_times = range(0, 23);
		$formated_times = array();
		foreach($review_times as $hour)
		{
			$nice = $hour. ' am';
			if($hour > 12)
			{
				$nice = ($hour - 12). ' pm';
			}
			if($hour == 12) $nice = 'Noon';
			if($hour == 0) $nice = 'Midnight';
			$formated_times[$hour] = $nice;
 		}
 		if($selected !== false) return $formated_times[$selected];
 		return $formated_times;
	}
}