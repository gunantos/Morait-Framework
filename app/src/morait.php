<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$time = $_SERVER['REQUEST_TIME'];
$timeout_duration = 3600;
if (isset($_SESSION['LAST_ACTIVITY']) && ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = $time;


if (file_exists(BASEPATH.'config/'.ENVIRONMENT.'/config.php'))
{
	require_once(BASEPATH.'config/'.ENVIRONMENT.'/config.php');
}

if (file_exists(BASEPATH.'config/config.php'))
{
	require_once(BASEPATH.'config/config.php');
}

require_once BASEPATH.'src/common.php';
require_once BASEPATH.'src/function.php';

if ( ! is_php('5.4'))
{
	ini_set('magic_quotes_runtime', 0);

	if ((bool) ini_get('register_globals'))
	{
		$_protected = array(
			'_SERVER',
			'_GET',
			'_POST',
			'_FILES',
			'_REQUEST',
			'_SESSION',
			'_ENV',
			'_COOKIE',
			'GLOBALS',
			'HTTP_RAW_POST_DATA',
			'system_path',
			'application_folder',
			'view_folder',
			'_protected',
			'_registered'
		);

		$_registered = ini_get('variables_order');
		foreach (array('E' => '_ENV', 'G' => '_GET', 'P' => '_POST', 'C' => '_COOKIE', 'S' => '_SERVER') as $key => $superglobal)
		{
			if (strpos($_registered, $key) === FALSE)
			{
				continue;
			}

			foreach (array_keys($$superglobal) as $var)
			{
				if (isset($GLOBALS[$var]) && ! in_array($var, $_protected, TRUE))
				{
					$GLOBALS[$var] = NULL;
				}
			}
		}
	}
}

if ( ! file_exists($file_path = FCPATH.'config/'.ENVIRONMENT.'/database.php')
	&& ! file_exists($file_path = FCPATH.'config/database.php'))
{
	show_error('The configuration file database.php does not exist.');
}

include($file_path);

if ( ! isset($db) OR count($db) === 0)
{
	show_error('No database connection settings were found in the database config file.');
}

/* Create New Database Class */
require_once(BASEPATH.'src/core/db_core.php');
class DB extends morait\db_core { }
DB::initialize($db);

require_once(BASEPATH.'src/core/app_core.php');
class APP extends morait\app_core { }
require_once(BASEPATH.'src/core/user_core.php');
class USER extends morait\user_core { }