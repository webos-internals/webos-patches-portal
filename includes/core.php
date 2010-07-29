<?php

/* COPYRIGHT 2009-2010 Daniel Beames and WebOS-Internals			*/
/* Redistribute only with explicit permission in writing from original author.	*/

if(!defined('IN_SCRIPT')) {
  die("Hacking attempt!!!");
}

set_magic_quotes_runtime(0);  // setting this to zero eliminates the need of stripslashes()
                              // stripslashes() is only required if php.ini contains magic_quotes_runtime = On.

// ################################# FUNCTIONS #################################

// addslashes to all post data

function AddSlashesArray($data) {
	if(is_array($data)) {
		foreach($data as $key => $val) {
			$return[$key] = AddSlashesArray($val);
	    }
	    return ($return);
	} else {
    	return (addslashes($data));
  	}
}

// ########################## ADDSLASHES TO POST DATA ##########################

if(!get_magic_quotes_gpc()) {  // add slashes if gpc is off
	$_POST   = AddSlashesArray($_POST);
	$_GET    = AddSlashesArray($_GET);
	$_COOKIE = AddSlashesArray($_COOKIE);
} // else add slashes has been performed automatically


// ########################## INCLUDE CONFIG ##########################

if(is_file($rootpath . 'includes/config.php')) {
	include($rootpath . 'includes/config.php');
}

// ########################## START DB CONNECTION ##########################

include($rootpath . 'includes/db/' . $dbtype . '.php');

$DB = new DB;

$DB->database = $dbname;
$DB->server   = $servername;
$DB->user     = $dbusername;
$DB->password = $dbpassword;

$DB->connect();

$dbpassword   = '';
$DB->password = '';

?>
