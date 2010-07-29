<?php

/* COPYRIGHT 2009-2010 Daniel Beames and WebOS-Internals			*/
/* Redistribute only with explicit permission in writing from original author.	*/

if(!defined('IN_SCRIPT')) {
  die("Hacking attempt!");
}

class DB {
	var $server   = "localhost";
	var $user     = "root";
	var $password = "";
	var $database = "";

	var $conn     = 0;
	var $queryid  = 0;
	var $row   = array();

	var $errdesc  = "";
	var $errno   = 0;


	// ###################### connect #######################
	function connect() {
		if(0 == $this->conn) {
			if($this->password=="") {
				$this->conn = mysql_connect($this->server,$this->user);
			} else {
				$this->conn = mysql_connect($this->server,$this->user,$this->password);
      		}

      		if(!$this->conn) {
        		$this->error("Connection == false, connect failed");
      		}

      		if($this->database != "") {
      			if(!mysql_select_db($this->database, $this->conn)) {
      				$this->error("cannot use database ".$this->database);
      			}
      		}

    	}
  	}

	// ###################### select database #######################
  	function select_db($database = "") {
  		if($database != "") {
  			$this->database = $database;
  		}

  		if(!mysql_select_db($this->database, $this->conn)) {
  			$this->error("cannot use database ".$this->database);
  		}
  	}

  	// ###################### query #######################
  	function query($query_string) {
  		$this->queryid = mysql_query($query_string,$this->conn);

  		if (!$this->queryid) {
  			$this->error("Invalid SQL: ".$query_string);
  		}

  		return $this->queryid;
  	}


	// ###################### query first #######################
  	function query_first($query_string) {
		$queryid    = $this->query($query_string);
		$returnarray = $this->fetch_array($queryid, $query_string);
    	$this->free_result($queryid);

    	return $returnarray;
  	}

  	// ###################### fetch array #######################
  	function fetch_array($queryid=-1,$query_string="") {
    	if($queryid != -1) {
    		$this->queryid=$queryid;
    	}
    	if(isset($this->queryid)) {
    		$this->row = mysql_fetch_array($this->queryid);
    	} else {
    		if(!empty($query_string)) {
        		$this->error("Invalid query id (".$this->queryid.") on query string: $query_string");
      		} else {
        		$this->error("Invalid query id: ".$this->queryid);
      		}
    	}

    	return $this->row;
 	}

  	// ###################### fetch array #######################
  	function affected_rows() {
	    if($this->conn) {
        	$result = @mysql_affected_rows($this->conn);
        	return $result;
        } else {
            return false;
        }
  	}

  	// ###################### free result #######################
  	function free_result($queryid=-1) {
  		if($queryid != -1) {
  			$this->queryid = $queryid;
  		}
  		return @mysql_free_result($this->queryid);
 	}



  	// ###################### number of rows #######################
  	function get_num_rows() {
    	return mysql_num_rows($this->queryid);
  	}


  	// ############ return last auto_increment number ##############
  	function insert_id() {
    	return mysql_insert_id($this->conn);
  	}

	// ###################### close the connection to the database #######################
  	function close() {
    	return mysql_close();
  	}

  	// ###################### get error description #######################
  	function geterrdesc() {
    	$this->error = mysql_error();
    	return $this->error;
  	}

  	// ###################### get error number #######################
  	function geterrno() {
    	$this->errno = mysql_errno();
    	return $this->errno;
  	}

  	// ###################### error message #######################
  	function error($msg) {
    	global $HTTP_SERVER_VARS;

    	$this->errdesc = mysql_error();
    	$this->errno   = mysql_errno();

//    	$gettechnicalemail = $this->query_first("SELECT value FROM " . TABLE_PREFIX . "mainsettings WHERE title = 'Technical Email'");
//    	$technicalemail    = $technicalemail['value'];
		$technicalemail = 'daniel@dbsooner.com';

    	$message  = "Database error in webos-patches: \r\n";
    	$message .= $msg." \r\n";
    	$message .= "Error: ".$this->errdesc." \r\n";
    	$message .= "Error number: ".$this->errno." \r\n";
    	$message .= "Date: ".gmdate("l dS of F Y h:i:s A")."\r\n";
    	$message .= "File: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

    	if(strlen($technicalemail) != 0) { // obtain emails
    		$getemails = str_replace(',', ' ', $technicalemail);           // get rid of commas
    		$getemails = eregi_replace("[[:space:]]+", " ", $getemails);   // get rid of extra spaces
    		$getemails = trim($getemails);                                 // then trim
    		$emails    = explode(" ", $getemails);

    		for($i = 0; $i < count($emails); $i++) {
        		@mail($emails[$i], "Webos-Patches Database Error!", $message, "From: $technicalemail");
      		}
    	}

    	echo '<b>Database Error.</b> <br />
         	  <p>A Database error occured, you can try fixing this problem by pressing the <a href="javascript:window.location=window.location;">Refresh</a> button in your browser.</p>
          	  <p>An Email regarding this problem has been sent to our Technical Staff.</p>';

//    	if(($_SESSION['mfladmin']) || ( $_SESSION['mflloggedin'] && $this->query_first("SELECT username FROM " . TABLE_PREFIX . "userprivileges WHERE username = '".addslashes($_SESSION['mflusername'])."'")) ) {
//			echo '<form><textarea rows="15" cols="60">'.htmlspecialchars($message).'</textarea></form>';
//    	}

    	exit;
  	}


}  // end class

?>
