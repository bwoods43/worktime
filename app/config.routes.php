<?php
/*
 * Routes are an associative multidimensional array. The parent array's
 * index is the regular expression to match against the Request URI 
 * and it's value should be an indexed array where the first element
 * is the controller name and the second element is the action.
 * 
 * Currently, there is no support for sending parameter values or 
 * backreferencing.
 * 
 */
$routes = array(
	'login' => array('index', 'login'),
	'logout' => array('index', 'logout'),
	'forgot' => array('index', 'forgot'),
	'admin\/users\/?.*' => array('admin_users', 'index'),
	'admin\/tasks\/?.*' => array('admin_tasks', 'index'),
	'admin\/projects\/?.*' => array('admin_projects', 'index'),
	'admin\/reports\/?.*' => array('admin_reports', 'index'),
	'managers\/reports\/?.*' => array('managers_reports', 'index')
);
