<?php

/* COPYRIGHT 2009-2010 Daniel Beames and WebOS-Internals			*/
/* Redistribute only with explicit permission in writing from original author. 	*/

session_start();
header("Cache-control: private");

error_reporting(E_ALL & ~E_NOTICE);
define('IN_SCRIPT', true);

$rootpath = './';
include($rootpath . 'includes/core.php');
include($rootpath . 'includes/functions.php');

// We Need to Be Sure URL's don't have PHPSESSID in it...

$URL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 
//Check if PHP is not in safe mode,
//and PHPSESSID is passed via URL
if (!ini_get ('safe_mode') && preg_match ('/'.session_name().'=([^=&\s]*)/i', $URL)) {
	//Remove PHPSESSID junk and unneeded characters ("&" or "?") at end of URL
	$URL = preg_replace ( array ('`'.session_name().'=([^=&\s]*)`', '`(&|\?)+$`'), '', $URL);
	//Send Moved Permanently header
	@header ("HTTP/1.1 301 Moved Permanently");
	//Redirect to clean URL
	@header ("Location: " . trim ($URL));
	//End current script
	exit();
}

// remove html, tags, and trim all post data
$_POST   = PreClean($_POST);
$_GET    = PreClean($_GET);
$_COOKIE = PreClean($_COOKIE);

// ############################ MAIN OUTPUT #################################

function MainHeader() {
	$do = $_GET['do'];
echo '<html>
	  <head>
	  <title>dBsooner\'s webOS-Patches Web Portal</title>
	  <link rel="stylesheet" href="http://www.dbsooner.com/webospatchuploadstyles.css" />
	  <meta http-equiv="expires" content="0" />
	  <meta http-equiv="Pragma" content="no-cache" />
	  </head>
	  <body>
	  <table width="100%" border="'.iif($do=="list", "1", "0").'" cellpadding="5" cellspacing="0">
	  <tr>
		<td colspan="10" align="center" class="header">WebsOS-Patches Web Portal<br/>
		<a href="/">Home</a> | <a href="?do=submit_new">Submit New Patch</a> | <a href="?do=submit_update">Submit Update</a> | '.iif($do=="list", "List Patches", '<a href="?do=list">List Patches</a>').'</td>
	  </tr>';
}

function ListPatches($webosver, $category) {
	global $DB, $webos_versions_array, $categories;
	echo '<tr>
			<td colspan="10" class="header2" align="center">';
	foreach($webos_versions_array as $key=>$webos_version) {
		$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE versions LIKE '%".$webos_version."%'");
		if($count['num'] > '0') {
			$versions[] = $webos_version;
		}
	}
	$i=0;
	foreach($versions as $key=>$version) {
		echo iif($i==0, "", " | ").iif($webosver==$version, '<font color="red">'.$version.'</font>', '<a href="?do=list&webosver='.$version.'">'.$version.'</a>');
		$i++;
	}
	echo iif($i==0, "", " | ").iif($webosver=="all", '<font color="red">All</font>', '<a href="?do=list&webosver=all">All</a>').'</td>
		</tr>';
	if($webosver) {
		echo '<tr>
				<td colspan="10" class="header2" align="center">';
		foreach($categories as $key=>$category1) {
			if($webosver == "all") {
				$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE category = '".$category1."'");
			} else {
				$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE category = '".$category1."' AND versions LIKE '%".$webosver."%'");
			}
			if($count['num'] > '0') {
				$category_list[] = $category1;
			}
		}
		$i=0;
		foreach($category_list as $key=>$category1) {
			echo iif($i==0, "", " | ").iif($category==$category1, '<font color="red">'.$category1.'</font>', '<a href="?do=list&webosver='.$webosver.'&category='.$category1.'" class="header2">'.$category1.'</a>');
			$i++;
		}
		echo iif($i==0, "", " | ").iif($category==all, '<font color="red">All</font>', '<a href="?do=list&webosver='.$webosver.'&category=all">All</a>').'</td>
			</tr>';
	}
	if($category) {
		echo '<tr>
				<td width="64px">&nbsp;</td>
				<td width="175px" align="center"><b>Title</b></td>
				<td width="275px" align="center"><b>Description</b></td>
				<td width="100px" align="center"><b>Category</b></td>
				<td width="30px" align="center"><b>SS1</b></td>
				<td width="30px" align="center"><b>SS2</b></td>
				<td width="30px" align="center"><b>SS3</b></td>
				<td width="75px" align="center"><b>Maintainer</b></td>
				<td width="50px" align="center"><b>Homepage</b></td>
				<td width="50px" align="center"><b>Versions</b></td>
		  	</tr>';
		if($webosver == 'all') {
			$webosver_query = '';
		} else {
			$webosver_query = $webosver;
		}
		if($category == 'all') {
			$category_query = '';
		} else {
			$category_query = " AND category = '".$category."'";
		}
		$getpatches = $DB->query("SELECT * FROM ".TABLE_PREFIX."patches WHERE status = '1' AND versions LIKE '%".$webosver_query."%'".$category_query);
		while($patch = $DB->fetch_array($getpatches)) {
			$maintainer_array = explode(',', $patch['maintainer']);
			$num_maintainers = count($maintainer_array);
			for($i=0; $i < $num_maintainers; $i++) {
				if($patch['email'] && ($patch['private'] != '1') && ($i=="0")) {
					$maintainer_out .= '<a href="mailto://'.$patch[email].'">'.ltrim($maintainer_array[$i]).'</a><br/>';
				} else {
					$maintainer_out .= ltrim($maintainer_array[$i]).'<br/>';
				}
			}
			$maintainer_array = array();
			$num_maintainers = 0;
			echo '<tr>
				<td><img src="'.$patch[icon].'"></img></td>
				<td>'.$patch[title].'</td>
				<td align="justify">'.$patch[description].'</td>
				<td align="center">'.$patch[category].'</td>
				<td align="center">'.iif(strlen($patch[screenshot_1])>=1, "<a href=\"".$patch[screenshot_1]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_2])>=1, "<a href=\"".$patch[screenshot_2]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_3])>=1, "<a href=\"".$patch[screenshot_3]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.$maintainer_out.'</td>
				<td align="center">'.iif(strlen($patch[homepage])>=1, "<a href=\"".$patch[homepage]."\" target=\"homepage\">Link</a>", "None").'</td>
				<td>'.str_replace(" ", "<br/>", $patch[versions]).iif(strlen($patch[changelog])>=1, "<br/><br/><a href=\"?do=get_changelog&pid=".$patch[pid]."\" target=\"changelog\">Changelog</a>", "").'</td>
			</tr>';
			$maintainer_out = NULL;
		}
	}
}

function BuildForm($errors, $pid) {
	global $DB, $categories, $webos_versions_array;
	echo '		<form name="submitpatch" method="post" action="/?do=submit_'.iif(strlen($pid)>=1, "update", "new").'" enctype="multipart/form-data">
		<tr>
			<td colspan="2" align="center" class="header">Submit Patch for webOS-Patches</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="header2">(*) = Required</td>
		  	<input type="hidden" name="submitaction" value="1">'.iif(strlen($pid)>=1, "<input type=\"hidden\" name=\"pid\" value=\"".$pid."\">", "").'
		</tr>';
	if(strlen($pid) >= "1" && (!$errors)) {
		$patch = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
		$get_webos_versions = explode(' ', $patch['versions']);
		foreach($get_webos_versions as $key=>$value) {
			$patch[webos_versions][] .= array_shift(explode('-',$value,2));
		}
	}
	if($errors) {
		$patch = $_POST;
		foreach($errors as $key => $value) {
			echo '		<tr>
			<td colspan="2" align="center" class="errors">'.$value.'<br/>
		</tr>';
		}
	}
	echo '		<tr>
			<td width="15%" class="'.iif($errors, "cell11", "cell3").'" valign="top">Title: (*)</td>
			<td width="85%" class="'.iif($errors, "cell12", "cell4").'"><input type="text" class="uploadpatch" name="title" value="'.FormatForForm($patch[title]).'" size="50" maxlength="40"'.iif(strlen($pid)>=1, "disabled><input type=\"hidden\" name=\"title\" value=\"".$patch[title]."\">", ">").'<br/>
			<b>Note:</b> Do not use category name, personalizations (your name, username,<br/>
			company name, tagline, etc), webOS Version. Be short and sweet. Limit 40<br/>
			characters. Numbers, Letters, apostrophes or spaces only.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Description: (*)</td>
			<td width="85%" class="cell4"><TEXTAREA class="uploadpatch" name="description" cols="50" rows="3">'.FormatForForm($patch[description]).'</TEXTAREA></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Patch File: (*)</td>
			<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="patch"></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Category: (*)</td>
			<td width="85%" class="cell4"><SELECT name="category" class="uploadpatch" '.iif(strlen($pid)>=1, "disabled", "").'>';
	foreach($categories as $key => $category) {
		echo '				<OPTION value="'.$category.'"';
		if($patch['category'] == $category) {
			echo ' SELECTED';
		}
		echo '>'.$category.'</OPTION>';
	}
	echo '			</SELECT>'.iif(strlen($pid)>=1, "<input type=\"hidden\" name=\"category\" value=\"".$patch[category]."\">", "").'</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 1:'.iif(strlen($patch[screenshot_1])>=1, "<br/><a href=\"".$patch[screenshot_1]."\">Current Image</a><input type=\"hidden\" name=\"screenshot_1\" value=\"".$patch[screenshot_1]."\">").'</td>
		  	<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="screenshot1"><br/>
			<b>Note:</b> Screenshots should be 320x480. They should not contain any other<br/>
			modifications in the picture as this can be misleading and cause confusion.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 2:'.iif(strlen($patch[screenshot_2])>=1, "<br/><a href=\"".$patch[screenshot_2]."\">Current Image</a><input type=\"hidden\" name=\"screenshot_2\" value=\"".$patch[screenshot_2]."\">").'</td>
			<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="screenshot2"><br/>
                        <b>Note:</b> Screenshots should be 320x480. They should not contain any other<br/>
                        modifications in the picture as this can be misleading and cause confusion.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 3:'.iif(strlen($patch[screenshot_3])>=1, "<br/><a href=\"".$patch[screenshot_3]."\">Current Image</a><input type=\"hidden\" name=\"screenshot_3\" value=\"".$patch[screenshot_3]."\">").'</td>
			<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="screenshot3"><br/>
                        <b>Note:</b> Screenshots should be 320x480. They should not contain any other<br/>
                        modifications in the picture as this can be misleading and cause confusion.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Compatible webOS Version(s): (*)</font></td>
			<td width="85%" class="cell4">';
	foreach($webos_versions_array as $key => $webos_version) {
		echo '			<input type="checkbox" name="webos_versions[]" value="'.$webos_version.'"';
		if($patch[webos_versions]) {
			if(in_array($webos_version, $patch[webos_versions])) {
				echo 'CHECKED';
			}
		}
		echo '>&nbsp;&nbsp;'.$webos_version.'<br/>';
	}
	echo '			</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Maintainer: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="maintainer" value="'.FormatForForm($patch[maintainer]).'" size="50" maxlength="50"><br/>
			<b>Note:</b> Use your PreCentral.net or webOS-Internals.org username if you do not<br/>
			want to give your real name. This information is published in the package\'s<br/>
			meta-data. It will be viewable by the public.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Email: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="email" value="'.FormatForForm($patch[email]).'" size="50" maxlength="128">&nbsp;&nbsp;<input type="checkbox" name="private" value="1"'.iif($patch["private"]==1, " CHECKED", "").'> Keep Private?<br/>
			<b>Note:</b> This information is published in the package\'s meta-data if the above box is not checked. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responsible for support. Giving your email makes it easier for a user to<br/>
			request support.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Patch Homepage:</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="homepage" value="'.FormatForForm($patch[homepage]).'" size="50" maxlength="256"><br/>
			<b>Note:</b> This information is published in the package\'s meta-data. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responisble for support. Giving a URL to the patch\'s thread on PreCentral.net<br/>
			or wiki page on webOS-Internals.org makes it easier for a user to request support.</td>
		</tr>';
	if($pid) {
		echo '<tr>
				<td width="15%" class="cell3" valign="top">Changelog:</td>
				<td width="85%" class="cell4"><textarea class="uploadpatch" name="changelog" cols="50" rows="3">'.FormatForForm($patch[changelog]).'</textarea><br/>
				<b>Note:</b> Recommended format is \'YYYY-MM-DD: What Was Done\'. Separate entries by newline.</td>
			</tr>';
	}
	echo '<tr>
			<td width="15%" class="cell3" valign="top">Note to Admins:</td>
			<td width="85%" class="cell4"><textarea class="uploadpatch" name="notes_to_admin" cols="50" rows="3">'.FormatForForm($patch[notes_to_admin]).'</textarea><br/>
			<b>Note:</b> This will not be published. It is simply a note to the Admins.</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="cell5"><input type="submit" value="Send it Off!"></td>
		</tr>
		</form>';
}

function HandleForm($pid) {

	global $DB, $icon_array	;

	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	foreach($_FILES as $key => $value) {
		$$key = $value;
	}

	$known_image_types = array(
	                         'image/pjpeg' => 'jpg',
	                         'image/jpeg'  => 'jpg',
	                         'image/bmp'   => 'bmp',
	                         'image/x-png' => 'png',
				 'image/png'   => 'png'
	                       );
	$allowedpatchext = array(".patch");

   	$patchext = strrchr(strtolower($patch['name']),'.');

	
	if(strlen($title) < '1') {
		$errors[] = 'You must enter a title.';
	} else {
		if(preg_replace('/^[0-9A-Za-z- ]*$/', '', $title) != "") {
			$errors[] = 'Title can only contain letters, numbers and -\'s.';
		}
	}

	if(strlen($description) < '10') {
		$errors[] = 'Please be more descriptive.';
	}
	if($patch['size']) {
		if(!in_array($patchext, $allowedpatchext)) {
			$errors[] = 'Only patch files are allowed.';
		} else {
			if($patch['size'] > 250000) {
				$errors[] = 'Patch cannot be larger than 250KB.';
			}
		}
	} else {
		$errors[] = 'You must include a patch file.';
	}
	if(($screenshot1['size']) && !$known_image_types[$screenshot1['type']]) {
		  	$errors[] = 'You can only choose jpg, jpeg, bmp or png images for screenshot 1.';
  	}
	if(($screenshot2['size']) && !$known_image_types[$screenshot2['type']]) {
		  	$errors[] = 'You can only choose jpg, jpeg, bmp or png images for screenshot 2.';
  	}
	if(($screenshot3['size']) && !$known_image_types[$screenshot3['type']]) {
		  	$errors[] = 'You can only choose jpg, jpeg, bmp or png images for screenshot 3.';
  	}
	if($category == 'Select...') {
		$errors[] = 'You must select a category.';
	}
	if(!$webos_versions) {
		$errors[] = 'You must select at least one webOS Version.';
	}
	if(!$maintainer) {
		$errors[] = 'You must enter the maintainer.';
	}
	if($email) {
		if(!validateEmail($email)) {
			$errors[] = 'Email address is invalid.';
		}
	} else {
		$errors[] = 'You must supply your email address.';
	}
	if($homepage) {
		if(!validateUrlSyntax($homepage, '')) {
			$errors[] = 'Patch homepage is invalid.';
		}
	}

	if($errors) {
		BuildForm($errors, $_POST['pid']);
		return;
	}

	$ver_count=0;
	foreach ($webos_versions as $key => $webos_version) {
		if($ver_count==0) {
			$versions = $webos_version;
		} else {
			$versions .= ' '.$webos_version;
		}
		$ver_count++;
	}
	$icon = $icon_array[$category];
	$screenshots = '0';
	if($screenshot1['size']) {
		$screenshots++;
	}
	if($screenshot2['size']) {
		$screenshots++;
	}	
	if($screenshot3['size']) {
		$screenshots++;
	}

	$description2 = mynl2br($description);
	$description2 = stripslashes(str_replace('"', "'", $description2));
	$changelog2 = mynl2br($changelog);
	$changelog2 = stripslashes(str_replace('"', "'", $changelog2));
	$patch_file = file_get_contents($patch['tmp_name'], FILE_BINARY);

	if($screenshots > '0') {
		if($screenshot1['size']) {
			$screenshot_1_type = $screenshot1['type'];
			$screenshot_1_blob = file_get_contents($screenshot1['tmp_name'], FILE_BINARY);
		}
		if($screenshot2['size']) {
			$screenshot_2_type = $screenshot2['type'];
			$screenshot_2_blob = file_get_contents($screenshot2['tmp_name'], FILE_BINARY);
		}
		if($screenshot3['size']) {
			$screenshot_3_type = $screenshot3['type'];
			$screenshot_3_blob = file_get_contents($screenshot3['tmp_name'], FILE_BINARY);
		}
	}

	if($pid) {
		$cur = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
		$extra  = ", update_pid, versions, meta_sub_version, depends, screenshot_1, screenshot_2, screenshot_3";
		$extra2 = ", '".$pid."', '".$cur[versions]."', '".$cur[meta_sub_version]."', '".$cur[depends]."', '".$cur[screenshot_1]."', '".$cur[screenshot_2]."', '".$cur[screenshot_3]."'";
	}

	$DB->query("INSERT INTO ".TABLE_PREFIX."patches (pid, title, description, patch_file, category, screenshot_1_blob, 
				screenshot_1_type, screenshot_2_blob, screenshot_2_type, screenshot_3_blob, screenshot_3_type, icon, 
				webos_versions, maintainer, email, private, homepage, changelog, notes_to_admin, status, datesubmitted".$extra.")
				VALUES ('',
						'".mysql_real_escape_string($title)."',
						'".mysql_real_escape_string($description2)."',
						'".mysql_real_escape_string($patch_file)."',
						'$category',
						'".mysql_real_escape_string($screenshot_1_blob)."',
						'$screenshot_1_type',
						'".mysql_real_escape_string($screenshot_2_blob)."',
						'$screenshot_2_type',
						'".mysql_real_escape_string($screenshot_3_blob)."',
						'$screenshot_3_type',
						'$icon',
						'$versions',
						'".mysql_real_escape_string($maintainer)."',
						'$email',
						'$private',
						'$homepage',
						'".mysql_real_escape_string($changelog2)."',
						'".mysql_real_escape_string($notes_to_admin)."',
						'0',
						'".time()."'".$extra2.")
				");
	$pid = $DB->insert_id(); 

	//Let the user know the outcome
	echo '			<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td colspan="2" align="center" class="header">Submit Patch for webOS-Patches - Results</td>
			</tr>
			<tr>
				<td colspan="2" class="header2">Thank you for submitting a patch! Look for it very soon in the webOS-Patches feed in Preware (and 
other programs that use the webOS-Patches feed)! If there are any questions, please contact <a 
href="mailto:webOS-Patches@dbsooner.com">webOS-Patches@dbsooner.com</a>. Please note you will not be able to submit another patch for 5 minutes. Do not use 
your browser\'s back button and try to submit again, it will not let you. Instead, <a href="webospatchupload.php">click here</a> and refresh that page. It will let 
you know when you can submit again. </td>
			</tr>
			</table>';
	$log = fopen("../webospatches.log", "a");
	$csvdata = GetRemoteAddress().",".time()."\n";
	fwrite($log, $csvdata);
	fclose($log);
}

function SpamWait() {
	echo '			<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td colspan="2" align="center" class="header">Submit Patch to webOS-Patches Admins</td>
			</tr>
			<tr>
				<td colspan="2" align="center" class="header2">You have submitted a patch within the past 5 minutes. Please try again later.</td>
			</tr>
			</table>';

}

function MainFooter() {
	echo '	  <tr>
		<td colspan="10" width="100%" align="center" class="copyright"><hr class="bodysep"><center>By submitting this form, you, the submitter, 
		explicitly agree you are either the original author of the patch or have the right to submit the patch under the <b>MIT Open Source
		License</b>. Further, you agree the patch will be licensed under the <b>MIT Open Source License</b>. This is to allow Palm to use the
		patch as-is to incorporate them into future webOS releases. Information regarding this license can be found at: 
		<a href="http://www.webos-internals.org/wiki/MIT_Open_Source_License_-_webOS-Patches" target="_blank">
		http://www.webos-internals.org/wiki/MIT_Open_Source_License_-_webOS-Patches</a><br/>
		&copy; 2009 - 2010 Daniel Beames (dBsooner) and webOS-Internals Group</center></td>
	  </tr>
	  </table>
	  </body>
	  </html>';
}

// LET'S BUILD THE PAGE!

switch($do) {
	case 'list':
		MainHeader();
		ListPatches($_GET['webosver'], $_GET['category']);
		MainFooter();
		break;
	case 'get_changelog':
		MainHeader();
		GetChangelog($_GET['pid']);
		MainFooter();
		break;
	case 'submit_new':
		MainHeader();
		if(!SpamCheck(GetRemoteAddress())) {
			switch($_POST['submitaction']) {
				case '1':
					HandleForm('');
					break;
				default:
					BuildForm('', '');
			}
		} else {
			SpamWait();
		}
		MainFooter();
		break;
	case 'submit_update':
		MainHeader();
		if(!$_POST['pid']) {
			global $DB;
			echo '<tr>
					<td colspan=2" class="header" align="center"><b>Select Patch to Update</b></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><form name="submit_update" method="post" action="/?do=submit_update"><select name="pid">';
			$getpatches = $DB->query("SELECT pid,title,category FROM ".TABLE_PREFIX."patches WHERE status = '1'");
			while($patch = $DB->fetch_array($getpatches)) {
				$output_array[$patch[pid]] = '<b>'.$patch[category].':</b> '.$patch[title];
			}
			asort($output_array);
			foreach($output_array as $pid=>$output) {
				echo '<OPTION value='.$pid.'>'.$output.'</OPTION>';
			}
			echo '</SELECT><br/><br/><input type="submit" value="Next">
					</td>
				</tr>
				</form>';
		} else {
			if(!SpamCheck(GetRemoteAddress())) {
				switch($_POST['submitaction']) {
					case '1':
						HandleForm($_POST['pid']);
						break;
					default:
						BuildForm('', $_POST['pid']);
				}
			} else {
				SpamWait();
			}
		}	
		MainFooter();
		break;
	default:
		MainHeader();
		MainFooter();
}
?>
