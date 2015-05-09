<?php
/**
 * FinFlow 1.0 - Runs a task
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

include_once 'task-init.php';

use FinFlow\TaskAssistant;
use FinFlow\UI;

//detect cli
if( TaskAssistant::is_browser() ){

	$task = strtolower( get('task') );

	if( $task ){

		$path = TaskAssistant::get_task_file_path($task);

		if( $path ){

			include_once $path;

		}
		else
			UI::fatal_error(__t('Ops! Can\'t find task  <em>%1s</em> ... ', $task), true, true, UI::MSG_WARN);

	}
	else
		UI::fatal_error(__t('Ops! No task selected to run. Please enter your task via url param <em>?task={task_name}</em>'), true, true, UI::MSG_WARN);

}
else{

	//TODO get task name from the $args

	$task   = strtolower( arg(1) );

	die("You are running from CLI");

}