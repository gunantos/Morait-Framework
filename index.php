<?php
session_start();
ini_set('max_execution_time', 300);
date_default_timezone_set('Asia/Jakarta');

define('ENVIRONMENT', 'development');

 switch (ENVIRONMENT)
{
  case 'development':
    error_reporting(-1);
    ini_set('display_errors', 1);
  break;

  case 'testing':
  case 'production':
    ini_set('display_errors', 0);
    if (version_compare(PHP_VERSION, '5.3', '>='))
    {
      error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
    }
    else
    {
      error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
    }
  break;

  default:
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'The application environment is not set correctly.';
    exit(1); // EXIT_ERROR
}

$system_path = 'app';
if (defined('STDIN'))
  {
    chdir(dirname(__FILE__));
  }

  if (($_temp = realpath($system_path)) !== FALSE)
  {
    $system_path = $_temp.DIRECTORY_SEPARATOR;
  }
  else
  {
    // Ensure there's a trailing slash
    $system_path = strtr(
      rtrim($system_path, '/\\'),
      '/\\',
      DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
    ).DIRECTORY_SEPARATOR;
  }

  if ( ! is_dir($system_path))
  {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503); 
    echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3); // EXIT_CONFIG
  }

  define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
  define('BASEPATH', $system_path);
  define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);


define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'app/autoload.php';
  

?>